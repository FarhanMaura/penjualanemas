<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'code'           => ['nullable', 'string', 'max:50', 'unique:payment_methods,code'],
            'type'           => ['required', 'in:bank_transfer,cash,qris,debit,credit,other'],
            'bank_name'      => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_name'   => ['nullable', 'string', 'max:150'],
            'instructions'   => ['nullable', 'string', 'max:1000'],
            'is_active'      => ['nullable', 'boolean'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = Str::slug($validated['name'], '_');
            // Ensure unique code
            $baseCode = $validated['code'];
            $i = 1;
            while (PaymentMethod::where('code', $validated['code'])->exists()) {
                $validated['code'] = $baseCode . '_' . $i++;
            }
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $request->input('sort_order', 0);

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', "Metode pembayaran '{$validated['name']}' berhasil ditambahkan.");
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'code'           => ['required', 'string', 'max:50', 'unique:payment_methods,code,' . $paymentMethod->id],
            'type'           => ['required', 'in:bank_transfer,cash,qris,debit,credit,other'],
            'bank_name'      => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_name'   => ['nullable', 'string', 'max:150'],
            'instructions'   => ['nullable', 'string', 'max:1000'],
            'is_active'      => ['nullable', 'boolean'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['sort_order'] = $request->input('sort_order', 0);

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', "Metode pembayaran '{$paymentMethod->name}' berhasil diperbarui.");
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $name = $paymentMethod->name;
        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', "Metode pembayaran '{$name}' berhasil dihapus.");
    }
}
