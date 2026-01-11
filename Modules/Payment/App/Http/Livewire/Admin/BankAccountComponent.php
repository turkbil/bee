<?php

namespace Modules\Payment\App\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Payment\App\Models\BankAccount;
use Illuminate\Validation\Rule;

class BankAccountComponent extends Component
{
    // Modal state
    public $showModal = false;
    public $editMode = false;
    public $bankAccountId = null;

    // Form fields
    public $bank_name = '';
    public $branch_name = '';
    public $branch_code = '';
    public $account_holder_name = '';
    public $account_number = '';
    public $iban = '';
    public $swift_code = '';
    public $currency = 'TRY';
    public $is_active = true;
    public $sort_order = 0;
    public $description = '';

    protected function rules()
    {
        $rules = [
            'bank_name' => 'required|string|max:100',
            'branch_name' => 'nullable|string|max:100',
            'branch_code' => 'nullable|string|max:20',
            'account_holder_name' => 'required|string|max:150',
            'account_number' => 'nullable|string|max:50',
            'iban' => [
                'required',
                'string',
                'max:34',
                Rule::unique('bank_accounts', 'iban')->ignore($this->bankAccountId, 'bank_account_id'),
            ],
            'swift_code' => 'nullable|string|max:11',
            'currency' => 'required|in:TRY,USD,EUR,GBP,RUB',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'description' => 'nullable|string|max:1000',
        ];

        return $rules;
    }

    protected $validationAttributes = [
        'bank_name' => 'Banka Adı',
        'account_holder_name' => 'Hesap Sahibi',
        'iban' => 'IBAN',
        'currency' => 'Para Birimi',
    ];

    public function render()
    {
        $bankAccounts = BankAccount::orderBy('sort_order')->orderBy('bank_name')->get();

        return view('payment::admin.livewire.bank-account-component', [
            'bankAccounts' => $bankAccounts,
        ])->layout('layouts.admin');
    }

    public function openModal()
    {
        $this->reset(['bank_name', 'branch_name', 'branch_code', 'account_holder_name', 
                     'account_number', 'iban', 'swift_code', 'description']);
        $this->currency = 'TRY';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->editMode = false;
        $this->bankAccountId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        
        $this->bankAccountId = $bankAccount->bank_account_id;
        $this->bank_name = $bankAccount->bank_name;
        $this->branch_name = $bankAccount->branch_name ?? '';
        $this->branch_code = $bankAccount->branch_code ?? '';
        $this->account_holder_name = $bankAccount->account_holder_name;
        $this->account_number = $bankAccount->account_number ?? '';
        $this->iban = $bankAccount->iban;
        $this->swift_code = $bankAccount->swift_code ?? '';
        $this->currency = $bankAccount->currency;
        $this->is_active = $bankAccount->is_active;
        $this->sort_order = $bankAccount->sort_order;
        $this->description = $bankAccount->description ?? '';
        
        $this->editMode = true;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name ?: null,
            'branch_code' => $this->branch_code ?: null,
            'account_holder_name' => $this->account_holder_name,
            'account_number' => $this->account_number ?: null,
            'iban' => strtoupper(preg_replace('/\s+/', '', $this->iban)), // Temizle ve uppercase
            'swift_code' => $this->swift_code ?: null,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'description' => $this->description ?: null,
        ];

        if ($this->editMode) {
            BankAccount::where('bank_account_id', $this->bankAccountId)->update($data);
            session()->flash('message', 'Banka hesabı başarıyla güncellendi.');
        } else {
            BankAccount::create($data);
            session()->flash('message', 'Banka hesabı başarıyla eklendi.');
        }

        $this->showModal = false;
    }

    public function toggleActive($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update(['is_active' => !$bankAccount->is_active]);
        
        session()->flash('message', 'Hesap durumu güncellendi.');
    }

    public function delete($id)
    {
        BankAccount::findOrFail($id)->delete();
        session()->flash('message', 'Banka hesabı silindi.');
    }
}
