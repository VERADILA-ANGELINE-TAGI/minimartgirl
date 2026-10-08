<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CashierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_transaction_checkout(): void
    {
        $response = $this->post(route('transaction.store'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_cashier_can_open_the_pos_and_see_active_products(): void
    {
        $cashier = User::factory()->create(['name' => 'Siti Kasir']);
        $this->createProduct('Teh Melati', '899000', '4500.00', 6);

        $response = $this->actingAs($cashier)->get(route('transaction.create'));

        $response->assertSee('Transaksi Kasir');
        $response->assertSee('value="Siti Kasir"', false);
        $response->assertSee('action="'.route('profile.name.update').'"', false);
        $response->assertSee('name="name"', false);
        $response->assertSee('action="'.route('transaction.products.import').'"', false);
        $response->assertSee(route('transaction.products.import-template'), false);
        $response->assertSee('Teh Melati');
        $response->assertSee('data-catalog');
        $response->assertSee('discount_percentage');
        $response->assertSee('amount_paid');
    }

    public function test_newly_added_products_appear_first_and_are_marked_as_new(): void
    {
        $cashier = User::factory()->create();
        $olderProduct = $this->createProduct('Produk Lama', '899100', '2000.00', 4);
        $olderProduct->forceFill(['created_at' => now()->subDays(10)])->save();
        $newProduct = $this->createProduct('Produk Terbaru', '899101', '3500.00', 6);

        $response = $this->actingAs($cashier)->get(route('transaction.create'));

        $response->assertViewHas('products', function ($products) use ($newProduct, $olderProduct): bool {
            return $products->first()['id'] === $newProduct->id
                && $products->first()['is_new'] === true
                && $products->last()['id'] === $olderProduct->id
                && $products->last()['is_new'] === false;
        });
        $response->assertSee('BARU');
        $response->assertSee('addToCart(product.id)', false);
    }

    public function test_cashier_can_add_a_new_product_with_an_image_from_the_transaction_page(): void
    {
        Storage::fake('public');
        $cashier = User::factory()->create();

        $response = $this->actingAs($cashier)->post(route('transaction.products.store'), [
            'name' => 'Susu Stroberi',
            'barcode' => '8991234567890',
            'category' => 'Minuman',
            'description' => 'Susu rasa stroberi',
            'price' => '8500.00',
            'stock' => '12',
            'image' => UploadedFile::fake()->image('susu-stroberi.jpg'),
        ]);

        $product = Product::query()->where('barcode', '8991234567890')->sole();
        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHas('success', 'Barang Susu Stroberi berhasil ditambahkan ke katalog.');
        $this->assertSame('Susu Stroberi', $product->name);
        $this->assertSame('Minuman', $product->category);
        $this->assertSame('Susu rasa stroberi', $product->description);
        $this->assertSame('8500.00', $product->price);
        $this->assertSame(12, $product->stock);
        $this->assertTrue($product->is_active);
        $this->assertNotNull($product->image);
        $this->assertTrue(Storage::disk('public')->exists($product->image));

        $catalogResponse = $this->get(route('transaction.create'));
        $catalogResponse->assertSee('Susu Stroberi');
        $catalogResponse->assertSee('BARU');
    }

    public function test_duplicate_product_barcode_is_rejected_without_creating_another_product(): void
    {
        $cashier = User::factory()->create();
        $this->createProduct('Produk Lama', '8991234567891', '5000.00', 3);

        $response = $this->actingAs($cashier)->from(route('transaction.create'))
            ->post(route('transaction.products.store'), [
                'name' => 'Produk Duplikat',
                'barcode' => '8991234567891',
                'price' => '7000.00',
                'stock' => '5',
            ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors([
            'barcode' => 'Kode batang sudah digunakan barang lain.',
        ]);
        $this->assertDatabaseCount('products', 1);
    }

    public function test_product_image_must_be_a_supported_image_file(): void
    {
        Storage::fake('public');
        $cashier = User::factory()->create();

        $response = $this->actingAs($cashier)->from(route('transaction.create'))
            ->post(route('transaction.products.store'), [
                'name' => 'Produk Dengan File Salah',
                'barcode' => '8991234567892',
                'price' => '7000.00',
                'stock' => '5',
                'image' => UploadedFile::fake()->create('malware.svg', 5, 'image/svg+xml'),
            ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('products', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_guest_cannot_add_a_product_from_the_transaction_page(): void
    {
        $response = $this->post(route('transaction.products.store'), [
            'name' => 'Barang Tamu',
            'barcode' => '8991234567893',
            'price' => '7000.00',
            'stock' => '5',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('products', 0);
    }

    public function test_cashier_can_bulk_import_products_from_excel_compatible_csv(): void
    {
        $cashier = User::factory()->create();
        $csv = "\xEF\xBB\xBFkode batang;nama;kategori;deskripsi;harga;stok\r\n"
            ."000123;\"Susu; Cokelat\";Minuman;\"Rasa; enak\";12500.00;10\r\n"
            ."000124;Roti;Makanan;;7000;5\r\n";

        $response = $this->actingAs($cashier)->post(route('transaction.products.import'), [
            'import_file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
        ]);

        $products = Product::query()->orderBy('barcode')->get();
        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHas('success', '2 produk berhasil diimpor ke katalog.');
        $this->assertDatabaseCount('products', 2);
        $this->assertSame('000123', $products[0]->barcode);
        $this->assertSame('Susu; Cokelat', $products[0]->name);
        $this->assertSame('Rasa; enak', $products[0]->description);
        $this->assertSame('12500.00', $products[0]->price);
        $this->assertSame(10, $products[0]->stock);
        $this->assertTrue($products[0]->is_active);
        $this->assertSame('000124', $products[1]->barcode);
        $this->assertSame('Makanan', $products[1]->category);
    }

    public function test_product_import_rejects_duplicate_barcodes_without_partially_importing(): void
    {
        $cashier = User::factory()->create();
        $this->createProduct('Produk Lama', '899111', '5000.00', 3);
        $csv = "barcode,name,category,description,price,stock\n"
            ."899112,Produk Baru,Minuman,,7000,5\n"
            ."899111,Produk Duplikat,Minuman,,8000,6\n";

        $response = $this->actingAs($cashier)->from(route('transaction.create'))
            ->post(route('transaction.products.import'), [
                'import_file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors([
            'import_file' => 'Baris 3: Kode batang 899111 sudah digunakan.',
        ]);
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseMissing('products', ['barcode' => '899112']);
    }

    public function test_product_import_rejects_duplicate_barcodes_within_the_file(): void
    {
        $cashier = User::factory()->create();
        $csv = "barcode,name,category,description,price,stock\n"
            ."899116,Produk Satu,Minuman,,7000,5\n"
            ."899116,Produk Dua,Minuman,,8000,6\n";

        $response = $this->actingAs($cashier)->from(route('transaction.create'))
            ->post(route('transaction.products.import'), [
                'import_file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors([
            'import_file' => 'Baris 2: Kode batang duplikat dalam berkas CSV.',
        ]);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_import_rejects_invalid_rows_without_creating_products(): void
    {
        $cashier = User::factory()->create();
        $csv = "barcode,name,category,description,price,stock\n"
            ."899113,Produk Valid,Minuman,,7000,5\n"
            ."899114,Produk Tidak Valid,Minuman,,7000,1.5\n";

        $response = $this->actingAs($cashier)->from(route('transaction.create'))
            ->post(route('transaction.products.import'), [
                'import_file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors('import_file');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_cashier_can_download_a_blank_product_import_template(): void
    {
        $cashier = User::factory()->create();

        $response = $this->actingAs($cashier)->get(route('transaction.products.import-template'));

        $response->assertDownload('template-impor-produk.csv');
        $response->assertStreamedContent("\xEF\xBB\xBF\"kode batang\",nama,kategori,deskripsi,harga,stok\n");
    }

    public function test_guest_cannot_import_products(): void
    {
        $this->get(route('transaction.products.import-template'))
            ->assertRedirect(route('login'));

        $response = $this->post(route('transaction.products.import'), [
            'import_file' => UploadedFile::fake()->createWithContent(
                'products.csv',
                "barcode,name,price,stock\n899115,Produk Tamu,7000,5\n"
            ),
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('products', 0);
    }

    public function test_cashier_can_save_paid_transaction_and_reduce_stock(): void
    {
        $cashier = User::factory()->create();
        $noodles = $this->createProduct('Mi Goreng', '899001', '12000.00', 5);
        $water = $this->createProduct('Air Mineral', '899002', '3000.00', 8);

        $response = $this->actingAs($cashier)->post(route('transaction.store'), [
            'products' => [
                ['id' => $noodles->id, 'quantity' => 2],
                ['id' => $water->id, 'quantity' => 1],
            ],
            'discount_percentage' => '10',
            'discount_amount' => '300',
            'tax' => '1500',
            'amount_paid' => '30000',
        ]);

        $transaction = Transaction::query()->sole();

        $response->assertRedirect(route('transaction.receipt', $transaction));
        $this->assertSame($cashier->id, $transaction->user_id);
        $this->assertSame('27000.00', $transaction->subtotal);
        $this->assertSame('3000.00', $transaction->discount);
        $this->assertSame('1500.00', $transaction->tax);
        $this->assertSame('25500.00', $transaction->total);
        $this->assertSame('30000.00', $transaction->amount_paid);
        $this->assertSame('4500.00', $transaction->change_amount);
        $this->assertSame('cash', $transaction->payment_method);
        $this->assertSame('paid', $transaction->payment_status);
        $this->assertMatchesRegularExpression('/^TRX-\d{8}-\d{4,}$/', $transaction->transaction_code);
        $response->assertSessionHas('success', "Transaksi {$transaction->transaction_code} berhasil disimpan.");

        $this->assertDatabaseHas('transaction_details', [
            'transaction_id' => $transaction->id,
            'product_id' => $noodles->id,
            'product_name' => 'Mi Goreng',
            'quantity' => 2,
            'unit_price' => '12000.00',
            'line_total' => '24000.00',
        ]);
        $this->assertSame(3, $noodles->fresh()->stock);
        $this->assertSame(7, $water->fresh()->stock);
    }

    public function test_electronic_payment_methods_are_recorded_as_paid_without_cash_change(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Minuman', '899020', '12500.00', 6);

        foreach (['qris', 'debit', 'credit', 'ewallet', 'transfer'] as $method) {
            $response = $this->actingAs($cashier)->post(route('transaction.store'), [
                'products' => [['id' => $product->id, 'quantity' => 1]],
                'discount_percentage' => '0',
                'discount_amount' => '0',
                'tax' => '0',
                'payment_method' => $method,
            ]);

            $transaction = Transaction::query()->latest('id')->firstOrFail();
            $response->assertRedirect(route('transaction.receipt', $transaction));
            $this->assertSame('paid', $transaction->payment_status);
            $this->assertSame($method, $transaction->payment_method);
            $this->assertSame('12500.00', $transaction->amount_paid);
            $this->assertSame('0.00', $transaction->change_amount);
        }

        $this->assertSame(1, $product->fresh()->stock);
        $this->assertDatabaseCount('transactions', 5);
    }

    public function test_cashier_can_hold_resume_and_pay_a_transaction_without_early_stock_reduction(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Kopi Bubuk', '899021', '18000.00', 5);

        $holdResponse = $this->actingAs($cashier)->post(route('transaction.store'), [
            'action' => 'hold',
            'products' => [['id' => $product->id, 'quantity' => 2]],
            'discount_percentage' => '5',
            'discount_amount' => '0',
            'tax' => '0',
        ]);

        $transaction = Transaction::query()->sole();
        $holdResponse->assertRedirect(route('transaction.create'));
        $this->assertSame('held', $transaction->payment_status);
        $this->assertSame('HLD-', substr($transaction->transaction_code, 0, 4));
        $this->assertSame(5, $product->fresh()->stock);
        $this->assertDatabaseHas('transaction_details', [
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $resumeResponse = $this->get(route('transaction.create', ['resume' => $transaction->id]));
        $resumeResponse->assertViewHas('resumeTransaction', $transaction);
        $resumeResponse->assertViewHas('initialCart', [
            ['id' => $product->id, 'quantity' => 2],
        ]);

        $payResponse = $this->post(route('transaction.store'), [
            'action' => 'pay',
            'held_transaction_id' => $transaction->id,
            'products' => [['id' => $product->id, 'quantity' => 2]],
            'discount_percentage' => '5',
            'discount_amount' => '0',
            'tax' => '0',
            'payment_method' => 'qris',
        ]);

        $payResponse->assertRedirect(route('transaction.receipt', $transaction));
        $payResponse->assertSessionHas('print_receipt', true);
        $this->assertDatabaseCount('transactions', 1);
        $this->assertSame('paid', $transaction->fresh()->payment_status);
        $this->assertSame('TRX-', substr($transaction->fresh()->transaction_code, 0, 4));
        $this->assertSame('34200.00', $transaction->fresh()->total);
        $this->assertSame(3, $product->fresh()->stock);

        $receiptResponse = $this->get(route('transaction.receipt', $transaction));
        $receiptResponse->assertSee('Cetak struk sekarang ke printer kasir?');
        $receiptResponse->assertSee('onclick="confirmReceiptPrint()"', false);
        $receiptResponse->assertSee('QRIS');
        $receiptResponse->assertSee('tidak diverifikasi oleh sistem');
    }

    public function test_only_the_cashier_who_owns_a_held_transaction_can_cancel_it(): void
    {
        $cashier = User::factory()->create();
        $otherCashier = User::factory()->create();
        $product = $this->createProduct('Biskuit Cokelat', '899022', '7000.00', 4);

        $this->actingAs($cashier)->post(route('transaction.store'), [
            'action' => 'hold',
            'products' => [['id' => $product->id, 'quantity' => 1]],
            'discount_percentage' => '0',
            'discount_amount' => '0',
            'tax' => '0',
        ]);
        $transaction = Transaction::query()->sole();

        $this->actingAs($otherCashier)
            ->post(route('transaction.cancel', $transaction))
            ->assertNotFound();
        $this->get(route('transaction.create', ['resume' => $transaction->id]))
            ->assertNotFound();
        $this->assertSame('held', $transaction->fresh()->payment_status);

        $cancelResponse = $this->actingAs($cashier)
            ->post(route('transaction.cancel', $transaction));

        $cancelResponse->assertRedirect(route('transaction.create'));
        $this->assertSame('cancelled', $transaction->fresh()->payment_status);
        $this->assertSame(4, $product->fresh()->stock);

        $historyResponse = $this->get(route('transaction.history', ['status' => 'cancelled']));
        $historyResponse->assertSee('Dibatalkan');
        $historyResponse->assertSee($transaction->transaction_code);
    }

    public function test_cashier_can_print_receipt_and_view_transaction_history(): void
    {
        $cashier = User::factory()->create(['name' => 'Budi Santoso']);
        $product = $this->createProduct('Teh Lemon', '899006', '6500.00', 5);
        $transaction = $this->createPaidTransaction($cashier, $product, 2);

        $receiptResponse = $this->actingAs($cashier)->get(route('transaction.receipt', $transaction));
        $receiptResponse->assertSee($transaction->transaction_code);
        $receiptResponse->assertSee('Budi Santoso');
        $receiptResponse->assertSee('Teh Lemon');
        $receiptResponse->assertSee('2 x Rp 6.500');
        $receiptResponse->assertSee('Rp 13.000');
        $receiptResponse->assertSee('window.print()', false);
        $receiptResponse->assertSee('function printReceipt()', false);
        $receiptResponse->assertSee('size: ${paperWidth}mm ${pageHeight}mm;', false);
        $receiptResponse->assertSee('data-paper-width="80"', false);
        $receiptResponse->assertSee('data-max-page-height="150"', false);
        $receiptResponse->assertSee('--receipt-width: 80mm;', false);
        $receiptResponse->assertSee('--receipt-print-padding: 3mm 4mm 4mm;', false);
        $receiptResponse->assertSee('function confirmReceiptPrint()', false);
        $receiptResponse->assertSee('let receiptPrintRequested = false;', false);
        $receiptResponse->assertSee('if (receiptPrintRequested)', false);
        $thermalReceiptResponse = $this->get(route('transaction.receipt', [
            'transaction' => $transaction,
            'paper' => '58',
        ]));
        $thermalReceiptResponse->assertSee('class="paper-58"', false);
        $thermalReceiptResponse->assertSee('data-paper-width="58"', false);
        $thermalReceiptResponse->assertSee('data-max-page-height="110"', false);
        $thermalReceiptResponse->assertSee('--receipt-width: 58mm;', false);
        $thermalReceiptResponse->assertSee('--receipt-print-padding: 3mm 3mm 4mm;', false);
        $thermalReceiptResponse->assertSee('size: ${paperWidth}mm ${pageHeight}mm;', false);

        $historyResponse = $this->get(route('transaction.history', [
            'search' => $transaction->transaction_code,
            'status' => 'paid',
        ]));
        $historyResponse->assertSee($transaction->transaction_code);
    }

    public function test_transaction_history_keeps_each_cashiers_name_as_it_was_when_the_transaction_was_created(): void
    {
        $cashier = User::factory()->create(['name' => 'Nama Saat Transaksi']);
        $otherCashier = User::factory()->create(['name' => 'Kasir Lain']);
        $product = $this->createProduct('Air Mineral', '899901', '5000.00', 4);
        $ownTransaction = $this->createPaidTransaction($cashier, $product, 1);
        $otherTransaction = $this->createPaidTransaction($otherCashier, $product, 1);
        $cashier->update(['name' => 'Nama Baru']);

        $response = $this->actingAs($cashier)
            ->withSession(['success' => null])
            ->get(route('transaction.history'));

        $response->assertSee($ownTransaction->transaction_code);
        $response->assertSee('Nama Saat Transaksi');
        $response->assertDontSee('Nama Baru');
        $response->assertSee($otherTransaction->transaction_code);
        $response->assertSee('Kasir Lain');

        $this->get(route('transaction.receipt', $ownTransaction))
            ->assertSee('Nama Saat Transaksi')
            ->assertDontSee('Nama Baru');
    }

    public function test_cashier_can_refund_items_partially_then_refund_transaction_fully(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Susu Kotak', '899007', '10000.00', 2);
        $transaction = $this->createPaidTransaction($cashier, $product, 2);
        $detail = $transaction->transactionDetails()->sole();

        $refundFormResponse = $this->get(route('transaction.refund.create', $transaction));
        $refundFormResponse->assertSee('Pengembalian Barang');
        $refundFormResponse->assertSee('Susu Kotak');
        $refundFormResponse->assertSee('Alasan pengembalian');

        $partialResponse = $this->post(route('transaction.refund.store', $transaction), [
            'items' => [['transaction_detail_id' => $detail->id, 'quantity' => 1]],
            'reason' => 'Kemasan rusak',
        ]);

        $partialResponse->assertRedirect(route('transaction.receipt', $transaction));
        $this->assertDatabaseHas('refunds', [
            'transaction_id' => $transaction->id,
            'user_id' => $cashier->id,
            'amount' => '10000.00',
            'reason' => 'Kemasan rusak',
        ]);
        $this->assertDatabaseHas('refund_details', [
            'transaction_detail_id' => $detail->id,
            'quantity' => 1,
            'amount' => '10000.00',
        ]);
        $this->assertSame(1, $product->fresh()->stock);
        $this->assertSame(1, $detail->fresh()->refunded_quantity);
        $this->assertSame('10000.00', $transaction->fresh()->refunded_amount);
        $this->assertSame('partially_refunded', $transaction->fresh()->payment_status);
        $historyResponse = $this->get(route('transaction.history', ['status' => 'partially_refunded']));
        $historyResponse->assertSee($transaction->transaction_code);
        $historyResponse->assertSee('Dikembalikan sebagian');
        $receiptResponse = $this->get(route('transaction.receipt', $transaction));
        $receiptResponse->assertSee('RFD-');
        $receiptResponse->assertSee('Kemasan rusak');

        $fullResponse = $this->post(route('transaction.refund.store', $transaction), [
            'items' => [['transaction_detail_id' => $detail->id, 'quantity' => 1]],
            'reason' => 'Pengembalian sisa item',
        ]);

        $fullResponse->assertRedirect(route('transaction.receipt', $transaction));
        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(2, $detail->fresh()->refunded_quantity);
        $this->assertSame('20000.00', $transaction->fresh()->refunded_amount);
        $this->assertSame('refunded', $transaction->fresh()->payment_status);
        $this->assertDatabaseCount('refunds', 2);
        $fullRefundFormResponse = $this->get(route('transaction.refund.create', $transaction));
        $fullRefundFormResponse->assertNotFound();
    }

    public function test_refunding_all_items_returns_the_exact_discounted_and_taxed_amount(): void
    {
        $cashier = User::factory()->create();
        $firstProduct = $this->createProduct('Produk A', '899010', '10.01', 4);
        $secondProduct = $this->createProduct('Produk B', '899011', '2.11', 4);

        $this->actingAs($cashier)->post(route('transaction.store'), [
            'products' => [
                ['id' => $firstProduct->id, 'quantity' => 1],
                ['id' => $secondProduct->id, 'quantity' => 2],
            ],
            'discount_percentage' => '10',
            'discount_amount' => '0',
            'tax' => '0.33',
            'amount_paid' => '15',
        ]);
        $transaction = Transaction::query()->sole();
        $items = $transaction->transactionDetails()->get();

        $response = $this->post(route('transaction.refund.store', $transaction), [
            'items' => $items->map(fn ($detail): array => [
                'transaction_detail_id' => $detail->id,
                'quantity' => $detail->quantity,
            ])->all(),
            'reason' => 'Transaksi dibatalkan',
        ]);

        $response->assertRedirect(route('transaction.receipt', $transaction));
        $this->assertSame('13.14', $transaction->fresh()->total);
        $this->assertSame('13.14', $transaction->fresh()->refunded_amount);
        $this->assertSame('refunded', $transaction->fresh()->payment_status);
        $this->assertSame(4, $firstProduct->fresh()->stock);
        $this->assertSame(4, $secondProduct->fresh()->stock);
    }

    public function test_refund_is_rejected_when_requested_quantity_exceeds_unreturned_items(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Biskuit', '899008', '5000.00', 4);
        $transaction = $this->createPaidTransaction($cashier, $product, 1);
        $detail = $transaction->transactionDetails()->sole();

        $response = $this->from(route('transaction.refund.create', $transaction))
            ->post(route('transaction.refund.store', $transaction), [
                'items' => [['transaction_detail_id' => $detail->id, 'quantity' => 2]],
                'reason' => 'Salah jumlah',
            ]);

        $response->assertRedirect(route('transaction.refund.create', $transaction));
        $response->assertSessionHasErrors([
            'items' => 'Jumlah barang Biskuit melebihi jumlah yang tersisa untuk dikembalikan (1).',
        ]);
        $this->assertDatabaseCount('refunds', 0);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame('paid', $transaction->fresh()->payment_status);
    }

    public function test_guest_cannot_open_history_or_receipt(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Air Mineral', '899009', '3000.00', 4);
        $transaction = $this->createPaidTransaction($cashier, $product, 1);
        Auth::logout();

        $historyResponse = $this->get(route('transaction.history'));
        $receiptResponse = $this->get(route('transaction.receipt', $transaction));

        $historyResponse->assertRedirect(route('login'));
        $receiptResponse->assertRedirect(route('login'));
    }

    public function test_transaction_is_rejected_when_cash_payment_is_insufficient(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Susu', '899003', '10000.00', 4);

        $response = $this->actingAs($cashier)->from(route('transaction.create'))->post(route('transaction.store'), [
            'products' => [['id' => $product->id, 'quantity' => 1]],
            'discount_percentage' => '0',
            'discount_amount' => '0',
            'tax' => '0',
            'amount_paid' => '9000',
        ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors([
            'amount_paid' => 'Uang pembayaran kurang dari total transaksi.',
        ]);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_transaction_is_rejected_when_cart_exceeds_available_stock(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Biskuit', '899004', '5000.00', 1);

        $response = $this->actingAs($cashier)->from(route('transaction.create'))->post(route('transaction.store'), [
            'products' => [['id' => $product->id, 'quantity' => 2]],
            'discount_percentage' => '0',
            'discount_amount' => '0',
            'tax' => '0',
            'amount_paid' => '10000',
        ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors([
            'products' => 'Stok Biskuit tidak cukup. Stok tersedia: 1.',
        ]);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_total_discount_cannot_exceed_subtotal(): void
    {
        $cashier = User::factory()->create();
        $product = $this->createProduct('Kopi', '899005', '7000.00', 3);

        $response = $this->actingAs($cashier)->from(route('transaction.create'))->post(route('transaction.store'), [
            'products' => [['id' => $product->id, 'quantity' => 1]],
            'discount_percentage' => '50',
            'discount_amount' => '4000',
            'tax' => '0',
            'amount_paid' => '10000',
        ]);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHasErrors([
            'discount_amount' => 'Total diskon tidak boleh melebihi jumlah sementara.',
        ]);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(3, $product->fresh()->stock);
    }

    private function createProduct(string $name, string $barcode, string $price, int $stock): Product
    {
        return Product::create([
            'name' => $name,
            'barcode' => $barcode,
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    private function createPaidTransaction(User $cashier, Product $product, int $quantity): Transaction
    {
        $this->actingAs($cashier)->post(route('transaction.store'), [
            'products' => [['id' => $product->id, 'quantity' => $quantity]],
            'discount_percentage' => '0',
            'discount_amount' => '0',
            'tax' => '0',
            'amount_paid' => (string) ((float) $product->price * $quantity),
        ]);

        return Transaction::query()->latest('id')->firstOrFail();
    }
}
