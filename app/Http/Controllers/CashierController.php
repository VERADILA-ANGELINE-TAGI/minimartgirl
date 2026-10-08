<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Refund;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashierController extends Controller
{
    private const MAX_AMOUNT_CENTS = 999_999_999_999;

    private const MAX_PRODUCT_IMPORT_ROWS = 5000;

    public function createTransaction(Request $request): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->latest('created_at')
            ->latest('id')
            ->get(['id', 'name', 'barcode', 'category', 'price', 'stock', 'image', 'created_at'])
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'category' => $product->category,
                'price' => (float) $product->price,
                'stock' => $product->stock,
                'is_new' => $product->created_at->greaterThanOrEqualTo(now()->subDays(7)),
                'image' => $product->image
                    ? asset('storage/'.ltrim($product->image, '/'))
                    : asset('images/products/'.$this->fallbackImage($product->name)),
            ]);

        $heldTransactions = Transaction::query()
            ->where('user_id', $request->user()->id)
            ->where('payment_status', 'held')
            ->withCount('transactionDetails')
            ->latest()
            ->get();

        $resumeTransaction = null;
        $initialCart = [];

        if ($request->filled('resume')) {
            $resumeTransaction = Transaction::query()
                ->where('user_id', $request->user()->id)
                ->where('payment_status', 'held')
                ->with('transactionDetails:id,transaction_id,product_id,quantity')
                ->findOrFail($request->integer('resume'));

            $initialCart = $resumeTransaction->transactionDetails
                ->map(fn ($detail): array => [
                    'id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                ])
                ->all();
        }

        $initialCart = old('products', $initialCart);

        $cashierName = $request->user()->name;

        return view('transaction', compact(
            'products',
            'heldTransactions',
            'resumeTransaction',
            'initialCart',
            'cashierName'
        ));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['required', 'string', 'max:255', 'unique:products,barcode'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama barang wajib diisi.',
            'barcode.required' => 'Kode batang wajib diisi.',
            'barcode.unique' => 'Kode batang sudah digunakan barang lain.',
            'price.gt' => 'Harga barang harus lebih dari nol.',
            'stock.min' => 'Stok tidak boleh kurang dari nol.',
            'image.image' => 'Berkas gambar harus berupa gambar yang valid.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');

            if ($imagePath === false) {
                throw new \RuntimeException('Gambar produk gagal disimpan ke penyimpanan publik.');
            }
        }

        Product::create([
            'name' => $validated['name'],
            'barcode' => $validated['barcode'],
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()
            ->route('transaction.create')
            ->with('success', "Barang {$validated['name']} berhasil ditambahkan ke katalog.");
    }

    public function downloadProductImportTemplate(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'wb');

            if ($output === false) {
                throw new \RuntimeException('Template impor produk gagal dibuat.');
            }

            if (fwrite($output, "\xEF\xBB\xBF") === false
                || fputcsv($output, ['kode batang', 'nama', 'kategori', 'deskripsi', 'harga', 'stok'], ',', '"', '') === false) {
                throw new \RuntimeException('Template impor produk gagal ditulis.');
            }

            fclose($output);
        }, 'template-impor-produk.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importProducts(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,txt', 'extensions:csv', 'max:5120'],
        ], [
            'import_file.required' => 'Pilih berkas CSV untuk diimpor.',
            'import_file.mimes' => 'Berkas impor harus berupa CSV yang valid.',
            'import_file.extensions' => 'Gunakan berkas dengan akhiran .csv.',
            'import_file.max' => 'Ukuran berkas CSV maksimal 5 MB.',
        ]);

        $file = $validated['import_file'];
        $handle = fopen($file->getRealPath(), 'rb');

        if ($handle === false) {
            throw new \RuntimeException('Berkas CSV produk gagal dibaca.');
        }

        try {
            $firstLine = fgets($handle);

            if ($firstLine === false) {
                return $this->redirectWithImportError('Berkas CSV kosong.');
            }

            $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
            $headers = str_getcsv(rtrim($firstLine, "\r\n"), $delimiter, '"', '\\');
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]) ?? (string) $headers[0];
            $headers = array_map(fn ($header): string => mb_strtolower(trim((string) $header)), $headers);
            $headerAliases = [
                'barcode' => 'barcode',
                'kode batang' => 'barcode',
                'kode_batang' => 'barcode',
                'nama' => 'name',
                'name' => 'name',
                'kategori' => 'category',
                'category' => 'category',
                'deskripsi' => 'description',
                'description' => 'description',
                'harga' => 'price',
                'price' => 'price',
                'stok' => 'stock',
                'stock' => 'stock',
            ];
            $unknownHeaders = array_diff($headers, array_keys($headerAliases));
            $headers = array_map(fn ($header): string => $headerAliases[$header] ?? $header, $headers);
            $requiredHeaders = ['barcode', 'name', 'price', 'stock'];
            $allowedHeaders = ['barcode', 'name', 'category', 'description', 'price', 'stock'];

            if (count($headers) !== count(array_unique($headers))
                || array_diff($requiredHeaders, $headers) !== []
                || $unknownHeaders !== []
                || array_diff($headers, $allowedHeaders) !== []) {
                return $this->redirectWithImportError(
                    'Judul kolom CSV wajib memuat kode batang, nama, harga, dan stok. Kolom tambahan yang dapat digunakan: kategori dan deskripsi. Unduh templat untuk format yang benar.'
                );
            }

            $products = [];
            $rowNumbers = [];
            $lineNumber = 1;

            while (($columns = fgetcsv($handle, null, $delimiter, '"', '\\')) !== false) {
                $lineNumber++;

                if ($columns === [null] || count(array_filter($columns, fn ($value): bool => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                if (count($columns) !== count($headers)) {
                    return $this->redirectWithImportError(
                        "Jumlah kolom pada baris {$lineNumber} tidak sesuai dengan judul kolom CSV."
                    );
                }

                $record = array_combine($headers, array_map(fn ($value): string => trim((string) $value), $columns));
                $products[] = [
                    'barcode' => $record['barcode'],
                    'name' => $record['name'],
                    'category' => $record['category'] ?? null,
                    'description' => $record['description'] ?? null,
                    'price' => $record['price'],
                    'stock' => $record['stock'],
                ];
                $rowNumbers[] = $lineNumber;

                if (count($products) > self::MAX_PRODUCT_IMPORT_ROWS) {
                    return $this->redirectWithImportError(
                        'Maksimal '.self::MAX_PRODUCT_IMPORT_ROWS.' produk dapat diimpor dalam satu berkas.'
                    );
                }
            }
        } finally {
            fclose($handle);
        }

        if ($products === []) {
            return $this->redirectWithImportError('Berkas CSV tidak berisi data produk.');
        }

        $validator = Validator::make(['products' => $products], [
            'products.*.barcode' => ['required', 'string', 'max:255'],
            'products.*.name' => ['required', 'string', 'max:255'],
            'products.*.category' => ['nullable', 'string', 'max:255'],
            'products.*.description' => ['nullable', 'string', 'max:2000'],
            'products.*.price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'products.*.stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ], [
            'products.*.barcode.required' => 'Kode batang wajib diisi.',
            'products.*.barcode.max' => 'Kode batang maksimal 255 karakter.',
            'products.*.name.required' => 'Nama produk wajib diisi.',
            'products.*.name.max' => 'Nama produk maksimal 255 karakter.',
            'products.*.category.max' => 'Kategori maksimal 255 karakter.',
            'products.*.description.max' => 'Deskripsi maksimal 2.000 karakter.',
            'products.*.price.required' => 'Harga wajib diisi.',
            'products.*.price.numeric' => 'Harga harus berupa angka dengan titik sebagai desimal.',
            'products.*.price.gt' => 'Harga harus lebih dari nol.',
            'products.*.price.max' => 'Harga melebihi batas maksimum.',
            'products.*.stock.required' => 'Stok wajib diisi.',
            'products.*.stock.integer' => 'Stok harus berupa bilangan bulat.',
            'products.*.stock.min' => 'Stok tidak boleh kurang dari nol.',
            'products.*.stock.max' => 'Stok melebihi batas maksimum.',
        ]);

        $importErrors = [];

        foreach ($validator->errors()->messages() as $attribute => $messages) {
            preg_match('/^products\.(\d+)\./', $attribute, $matches);
            $rowNumber = $rowNumbers[(int) ($matches[1] ?? 0)] ?? 2;

            foreach ($messages as $message) {
                $importErrors[] = "Baris {$rowNumber}: {$message}";
            }
        }

        $barcodeCounts = array_count_values(array_column($products, 'barcode'));
        $existingBarcodes = Product::query()
            ->whereIn('barcode', array_keys($barcodeCounts))
            ->pluck('barcode')
            ->all();

        foreach ($products as $index => $product) {
            if ($product['barcode'] !== '' && ($barcodeCounts[$product['barcode']] ?? 0) > 1) {
                $importErrors[] = 'Baris '.$rowNumbers[$index].': Kode batang duplikat dalam berkas CSV.';
            } elseif (in_array($product['barcode'], $existingBarcodes, true)) {
                $importErrors[] = 'Baris '.$rowNumbers[$index].': Kode batang '.$product['barcode'].' sudah digunakan.';
            }
        }

        if ($importErrors !== []) {
            $shownErrors = array_slice($importErrors, 0, 10);
            $remainingErrors = count($importErrors) - count($shownErrors);

            if ($remainingErrors > 0) {
                $shownErrors[] = "Masih ada {$remainingErrors} kesalahan lainnya. Perbaiki berkas lalu coba lagi.";
            }

            return redirect()
                ->route('transaction.create')
                ->withErrors(['import_file' => $shownErrors])
                ->with('open_product_import', true);
        }

        DB::transaction(function () use ($products): void {
            foreach ($products as $product) {
                Product::create([
                    ...$product,
                    'image' => null,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()
            ->route('transaction.create')
            ->with('success', count($products).' produk berhasil diimpor ke katalog.');
    }

    private function redirectWithImportError(string $message): RedirectResponse
    {
        return redirect()
            ->route('transaction.create')
            ->withErrors(['import_file' => $message])
            ->with('open_product_import', true);
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['nullable', 'in:pay,hold'],
            'held_transaction_id' => ['nullable', 'integer', 'exists:transactions,id'],
            'products' => ['required', 'array', 'min:1', 'max:100'],
            'products.*.id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'tax' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'payment_method' => ['nullable', Rule::in(['cash', 'qris', 'debit', 'credit', 'ewallet', 'transfer'])],
            'amount_paid' => [
                'nullable',
                Rule::requiredIf(
                    ($request->input('action', 'pay') === 'pay')
                    && ($request->input('payment_method', 'cash') === 'cash')
                ),
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
        ], [
            'products.required' => 'Tambahkan setidaknya satu produk ke keranjang.',
            'products.min' => 'Tambahkan setidaknya satu produk ke keranjang.',
            'products.*.quantity.min' => 'Jumlah setiap produk minimal satu.',
            'discount_percentage.max' => 'Diskon persentase tidak boleh lebih dari 100%.',
            'amount_paid.min' => 'Uang diterima tidak boleh kurang dari nol.',
        ]);

        $lines = collect($validated['products'])
            ->mapWithKeys(fn (array $item): array => [(int) $item['id'] => (int) $item['quantity']]);
        $action = $validated['action'] ?? 'pay';
        $paymentMethod = $validated['payment_method'] ?? 'cash';
        $discountPercentage = (float) $validated['discount_percentage'];
        $discountAmountCents = $this->toCents($validated['discount_amount']);
        $taxCents = $this->toCents($validated['tax']);
        $amountPaidCents = isset($validated['amount_paid']) ? $this->toCents($validated['amount_paid']) : 0;
        $cashierId = $request->user()->id;
        $cashierName = $request->user()->name;
        $heldTransactionId = isset($validated['held_transaction_id']) ? (int) $validated['held_transaction_id'] : null;

        $transaction = DB::transaction(function () use (
            $action,
            $paymentMethod,
            $lines,
            $discountPercentage,
            $discountAmountCents,
            $taxCents,
            $amountPaidCents,
            $cashierId,
            $cashierName,
            $heldTransactionId
        ): Transaction {
            $products = Product::query()
                ->whereIn('id', $lines->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $lines->count()) {
                throw ValidationException::withMessages([
                    'products' => 'Salah satu produk sudah tidak tersedia. Perbarui keranjang dan coba lagi.',
                ]);
            }

            $subtotalCents = 0;
            $detailLines = [];

            foreach ($lines as $productId => $quantity) {
                $product = $products->get($productId);

                if (! $product->is_active) {
                    throw ValidationException::withMessages([
                        'products' => "Produk {$product->name} sudah tidak aktif.",
                    ]);
                }

                if ($action === 'pay' && $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'products' => "Stok {$product->name} tidak cukup. Stok tersedia: {$product->stock}.",
                    ]);
                }

                $unitPriceCents = $this->toCents($product->price);

                if ($unitPriceCents > intdiv(self::MAX_AMOUNT_CENTS, $quantity)) {
                    throw ValidationException::withMessages([
                        'products' => 'Nilai transaksi melebihi batas yang dapat diproses.',
                    ]);
                }

                $lineTotalCents = $unitPriceCents * $quantity;
                $subtotalCents += $lineTotalCents;

                if ($subtotalCents > self::MAX_AMOUNT_CENTS) {
                    throw ValidationException::withMessages([
                        'products' => 'Nilai transaksi melebihi batas yang dapat diproses.',
                    ]);
                }

                $detailLines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $this->fromCents($unitPriceCents),
                    'line_total' => $this->fromCents($lineTotalCents),
                    'line_total_cents' => $lineTotalCents,
                ];
            }

            $percentageDiscountCents = (int) round(
                $subtotalCents * $discountPercentage / 100,
                0,
                PHP_ROUND_HALF_UP
            );
            $totalDiscountCents = $percentageDiscountCents + $discountAmountCents;

            if ($totalDiscountCents > $subtotalCents) {
                throw ValidationException::withMessages([
                    'discount_amount' => 'Total diskon tidak boleh melebihi jumlah sementara.',
                ]);
            }

            $totalCents = $subtotalCents - $totalDiscountCents + $taxCents;

            if ($totalCents > self::MAX_AMOUNT_CENTS) {
                throw ValidationException::withMessages([
                    'tax' => 'Total transaksi melebihi batas yang dapat diproses.',
                ]);
            }

            if ($action === 'pay' && $paymentMethod !== 'cash') {
                $amountPaidCents = $totalCents;
            }

            if ($action === 'pay' && $amountPaidCents < $totalCents) {
                throw ValidationException::withMessages([
                    'amount_paid' => 'Uang pembayaran kurang dari total transaksi.',
                ]);
            }

            $transaction = $heldTransactionId
                ? Transaction::query()
                    ->whereKey($heldTransactionId)
                    ->where('user_id', $cashierId)
                    ->where('payment_status', 'held')
                    ->lockForUpdate()
                    ->first()
                : null;

            if ($heldTransactionId && ! $transaction) {
                throw ValidationException::withMessages([
                    'held_transaction_id' => 'Transaksi tertahan tidak tersedia lagi.',
                ]);
            }

            $transactionAttributes = [
                'subtotal' => $this->fromCents($subtotalCents),
                'discount_percentage' => number_format($discountPercentage, 2, '.', ''),
                'discount_amount' => $this->fromCents($discountAmountCents),
                'discount' => $this->fromCents($totalDiscountCents),
                'tax' => $this->fromCents($taxCents),
                'total' => $this->fromCents($totalCents),
                'payment_method' => $paymentMethod,
                'amount_paid' => $this->fromCents($action === 'pay' ? $amountPaidCents : 0),
                'change_amount' => $this->fromCents($action === 'pay' && $paymentMethod === 'cash' ? $amountPaidCents - $totalCents : 0),
                'payment_status' => $action === 'hold' ? 'held' : 'paid',
            ];

            if ($transaction) {
                $transaction->update($transactionAttributes);
                $transaction->transactionDetails()->delete();
            } else {
                $transaction = Transaction::create([
                    'user_id' => $cashierId,
                    'cashier_name' => $cashierName,
                    'transaction_code' => 'PENDING-'.Str::uuid(),
                    ...$transactionAttributes,
                ]);
            }

            $transaction->update([
                'transaction_code' => sprintf(
                    '%s-%s-%04d',
                    $action === 'hold' ? 'HLD' : 'TRX',
                    now()->format('Ymd'),
                    $transaction->id
                ),
            ]);

            $allocatedNetCents = 0;
            $lastLineIndex = array_key_last($detailLines);

            foreach ($detailLines as $index => $line) {
                $product = $line['product'];
                $netTotalCents = $subtotalCents === 0
                    ? ($index === 0 ? $totalCents : 0)
                    : (int) floor(($line['line_total_cents'] / $subtotalCents) * $totalCents);
                $allocatedNetCents += $netTotalCents;

                if ($index === $lastLineIndex) {
                    $netTotalCents += $totalCents - $allocatedNetCents;
                }

                $transaction->transactionDetails()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'barcode' => $product->barcode,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                    'net_total' => $this->fromCents($netTotalCents),
                ]);

                if ($action === 'pay') {
                    $product->decrement('stock', $line['quantity']);
                }
            }

            return $transaction;
        });

        if ($action === 'hold') {
            return redirect()
                ->route('transaction.create')
                ->with('success', "Transaksi {$transaction->transaction_code} berhasil ditahan.");
        }

        return redirect()
            ->route('transaction.receipt', $transaction)
            ->with('success', "Transaksi {$transaction->transaction_code} berhasil disimpan.")
            ->with('print_receipt', true);
    }

    public function cancelHeldTransaction(Request $request, Transaction $transaction): RedirectResponse
    {
        $transaction = DB::transaction(function () use ($transaction, $request): Transaction {
            $heldTransaction = Transaction::query()
                ->whereKey($transaction->id)
                ->where('user_id', $request->user()->id)
                ->where('payment_status', 'held')
                ->lockForUpdate()
                ->firstOrFail();

            $heldTransaction->update(['payment_status' => 'cancelled']);

            return $heldTransaction;
        });

        return redirect()
            ->route('transaction.create')
            ->with('success', "Transaksi {$transaction->transaction_code} dibatalkan.");
    }

    public function transactionHistory(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:held,cancelled,paid,partially_refunded,refunded'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $transactions = Transaction::query()
            ->withCount('transactionDetails')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('transaction_code', 'like', "%{$search}%")
                        ->orWhere('cashier_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('payment_status', $status))
            ->when($filters['from'] ?? null, fn ($query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('transaction-history', compact('transactions', 'filters'));
    }

    public function showRefundForm(Transaction $transaction): View
    {
        abort_unless(in_array($transaction->payment_status, ['paid', 'partially_refunded'], true), 404);

        $transaction->load(['transactionDetails']);

        return view('transaction-refund', compact('transaction'));
    }

    public function storeRefund(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.transaction_detail_id' => ['required', 'integer', 'distinct', 'exists:transaction_details,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'items.required' => 'Pilih setidaknya satu barang yang akan dikembalikan.',
            'items.min' => 'Pilih setidaknya satu barang yang akan dikembalikan.',
            'reason.required' => 'Alasan pengembalian wajib diisi.',
        ]);

        $requestedItems = collect($validated['items'])
            ->mapWithKeys(fn (array $item): array => [
                (int) $item['transaction_detail_id'] => (int) $item['quantity'],
            ]);
        $cashierId = $request->user()->id;

        $refund = DB::transaction(function () use ($transaction, $requestedItems, $validated, $cashierId): Refund {
            $lockedTransaction = Transaction::query()
                ->whereKey($transaction->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedTransaction->payment_status === 'refunded') {
                throw ValidationException::withMessages([
                    'items' => 'Seluruh barang pada transaksi ini sudah dikembalikan.',
                ]);
            }

            if (! in_array($lockedTransaction->payment_status, ['paid', 'partially_refunded'], true)) {
                throw ValidationException::withMessages([
                    'items' => 'Pengembalian hanya dapat diproses untuk transaksi yang sudah dibayar.',
                ]);
            }

            $details = $lockedTransaction->transactionDetails()
                ->whereIn('id', $requestedItems->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($details->count() !== $requestedItems->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Barang yang dipilih tidak cocok dengan transaksi ini.',
                ]);
            }

            $refundLines = [];
            $refundAmountCents = 0;

            foreach ($requestedItems as $detailId => $quantity) {
                $detail = $details->get($detailId);
                $remainingQuantity = $detail->quantity - $detail->refunded_quantity;

                if ($quantity > $remainingQuantity) {
                    throw ValidationException::withMessages([
                        'items' => "Jumlah barang {$detail->product_name} melebihi jumlah yang tersisa untuk dikembalikan ({$remainingQuantity}).",
                    ]);
                }

                $newRefundedQuantity = $detail->refunded_quantity + $quantity;
                $netTotalCents = $this->toCents($detail->net_total);
                $previouslyRefundedCents = $this->toCents($detail->refunded_amount);
                $cumulativeRefundCents = $newRefundedQuantity === $detail->quantity
                    ? $netTotalCents
                    : (int) floor(($netTotalCents / $detail->quantity) * $newRefundedQuantity);
                $lineRefundCents = $cumulativeRefundCents - $previouslyRefundedCents;

                if ($lineRefundCents < 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Nilai pengembalian tidak valid. Hubungi pengelola.',
                    ]);
                }

                $refundAmountCents += $lineRefundCents;
                $refundLines[] = [
                    'detail' => $detail,
                    'quantity' => $quantity,
                    'new_refunded_quantity' => $newRefundedQuantity,
                    'cumulative_refund' => $this->fromCents($cumulativeRefundCents),
                    'amount' => $this->fromCents($lineRefundCents),
                ];
            }

            $transactionRefundedCents = $this->toCents($lockedTransaction->refunded_amount) + $refundAmountCents;

            if ($transactionRefundedCents > $this->toCents($lockedTransaction->total)) {
                throw ValidationException::withMessages([
                    'items' => 'Nilai pengembalian melebihi jumlah pembayaran transaksi.',
                ]);
            }

            $refund = $lockedTransaction->refunds()->create([
                'user_id' => $cashierId,
                'refund_code' => 'PENDING-'.Str::uuid(),
                'amount' => $this->fromCents($refundAmountCents),
                'reason' => $validated['reason'],
            ]);
            $refund->update([
                'refund_code' => sprintf('RFD-%s-%04d', now()->format('Ymd'), $refund->id),
            ]);

            foreach ($refundLines as $line) {
                $detail = $line['detail'];
                $product = Product::query()
                    ->whereKey($detail->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $refund->details()->create([
                    'transaction_detail_id' => $detail->id,
                    'product_name' => $detail->product_name,
                    'quantity' => $line['quantity'],
                    'amount' => $line['amount'],
                ]);

                $detail->update([
                    'refunded_quantity' => $line['new_refunded_quantity'],
                    'refunded_amount' => $line['cumulative_refund'],
                ]);
                $product->increment('stock', $line['quantity']);
            }

            $lockedTransaction->update([
                'refunded_amount' => $this->fromCents($transactionRefundedCents),
                'payment_status' => $lockedTransaction->transactionDetails()
                    ->whereColumn('refunded_quantity', '<', 'quantity')
                    ->exists()
                    ? 'partially_refunded'
                    : 'refunded',
            ]);

            return $refund;
        });

        return redirect()
            ->route('transaction.receipt', $transaction)
            ->with('success', "Pengembalian {$refund->refund_code} berhasil disimpan.");
    }

    private function toCents(int|float|string $amount): int
    {
        $formatted = number_format((float) $amount, 2, '.', '');
        [$whole, $fraction] = explode('.', $formatted);

        return ((int) $whole * 100) + (int) $fraction;
    }

    private function fromCents(int $amount): string
    {
        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }

    private function fallbackImage(string $name): string
    {
        return match (mb_strtolower($name)) {
            'indomie goreng' => 'indomie-goreng.svg',
            'teh botol' => 'teh-botol.svg',
            'roti coklat' => 'roti-coklat.svg',
            'air mineral' => 'air-mineral.svg',
            'biskuit coklat' => 'biskuit-coklat.svg',
            default => 'product-placeholder.svg',
        };
    }
}
