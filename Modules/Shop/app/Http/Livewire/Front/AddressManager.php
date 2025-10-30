<?php

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Shop\App\Models\ShopCustomer;
use Modules\Shop\App\Models\ShopCustomerAddress;
use Illuminate\Support\Facades\Auth;

class AddressManager extends Component
{
    public $customerId;
    public $addressType; // 'billing' veya 'shipping'
    public $addresses = [];
    public $selectedAddressId;

    // Modal states
    public $showSelectModal = false;
    public $showEditModal = false;
    public $editingAddressId = null;

    // Form fields
    public $title;
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $company_name;
    public $tax_office;
    public $tax_number;
    public $address_line_1;
    public $address_line_2;
    public $neighborhood;
    public $district;
    public $city;
    public $postal_code;
    public $delivery_notes;
    public $is_default = false;

    // İl/İlçe listesi
    public $cities = [];
    public $districts = [];

    protected $listeners = ['addressUpdated' => 'loadAddresses'];

    public function mount($customerId, $addressType = 'shipping', $selectedAddressId = null)
    {
        $this->customerId = $customerId;
        $this->addressType = $addressType;
        $this->selectedAddressId = $selectedAddressId;

        $this->loadCities();
        $this->loadAddresses();
    }

    public function loadCities()
    {
        // Turkey cities paketi kullanarak şehirleri yükle
        $this->cities = \DB::table('cities')->pluck('name', 'id')->toArray();
    }

    public function updatedCity($cityId)
    {
        // Şehir seçilince ilçeleri yükle
        $this->districts = \DB::table('districts')
            ->where('city_id', $cityId)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function loadAddresses()
    {
        if (!$this->customerId) {
            return;
        }

        $query = ShopCustomerAddress::where('customer_id', $this->customerId);

        if ($this->addressType === 'billing') {
            $query->billing();
        } elseif ($this->addressType === 'shipping') {
            $query->shipping();
        }

        $this->addresses = $query->orderBy('is_default_' . $this->addressType, 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Varsayılan adresi otomatik seç
        if (!$this->selectedAddressId && $this->addresses->count() > 0) {
            $defaultKey = 'is_default_' . $this->addressType;
            $default = $this->addresses->firstWhere($defaultKey, true);

            if ($default) {
                $this->selectedAddressId = $default->address_id;
            } else {
                $this->selectedAddressId = $this->addresses->first()->address_id;
            }
        }
    }

    public function openSelectModal()
    {
        $this->loadAddresses();
        $this->showSelectModal = true;
    }

    public function openEditModal($addressId = null)
    {
        $this->resetForm();

        if ($addressId) {
            $address = ShopCustomerAddress::find($addressId);
            if ($address && $address->customer_id == $this->customerId) {
                $this->editingAddressId = $addressId;
                $this->fillForm($address);
            }
        }

        $this->showEditModal = true;
    }

    public function selectAddress($addressId)
    {
        $this->selectedAddressId = $addressId;
        $this->showSelectModal = false;

        // Parent component'e bildir
        $this->emit('addressSelected', $addressId, $this->addressType);
    }

    public function saveAddress()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
        ], [
            'first_name.required' => 'Ad zorunludur',
            'last_name.required' => 'Soyad zorunludur',
            'phone.required' => 'Telefon zorunludur',
            'address_line_1.required' => 'Adres zorunludur',
            'city.required' => 'Şehir zorunludur',
            'district.required' => 'İlçe zorunludur',
        ]);

        $data = [
            'customer_id' => $this->customerId,
            'address_type' => $this->addressType,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'tax_office' => $this->tax_office,
            'tax_number' => $this->tax_number,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'neighborhood' => $this->neighborhood,
            'district' => $this->district,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'delivery_notes' => $this->delivery_notes,
        ];

        if ($this->editingAddressId) {
            // Güncelle
            $address = ShopCustomerAddress::find($this->editingAddressId);
            $address->update($data);
        } else {
            // Yeni oluştur
            $address = ShopCustomerAddress::create($data);
        }

        // Varsayılan olarak işaretle
        if ($this->is_default) {
            if ($this->addressType === 'billing') {
                $address->setAsDefaultBilling();
            } else {
                $address->setAsDefaultShipping();
            }
        }

        $this->showEditModal = false;
        $this->loadAddresses();

        session()->flash('address_success', 'Adres başarıyla kaydedildi');

        // Otomatik seç
        $this->selectAddress($address->address_id);
    }

    public function deleteAddress($addressId)
    {
        $address = ShopCustomerAddress::find($addressId);

        if ($address && $address->customer_id == $this->customerId) {
            $address->delete();
            $this->loadAddresses();

            session()->flash('address_success', 'Adres silindi');
        }
    }

    public function setAsDefault($addressId)
    {
        $address = ShopCustomerAddress::find($addressId);

        if ($address && $address->customer_id == $this->customerId) {
            if ($this->addressType === 'billing') {
                $address->setAsDefaultBilling();
            } else {
                $address->setAsDefaultShipping();
            }

            $this->loadAddresses();
            session()->flash('address_success', 'Varsayılan adres güncellendi');
        }
    }

    private function fillForm($address)
    {
        $this->first_name = $address->first_name;
        $this->last_name = $address->last_name;
        $this->phone = $address->phone;
        $this->email = $address->email;
        $this->company_name = $address->company_name;
        $this->tax_office = $address->tax_office;
        $this->tax_number = $address->tax_number;
        $this->address_line_1 = $address->address_line_1;
        $this->address_line_2 = $address->address_line_2;
        $this->neighborhood = $address->neighborhood;
        $this->district = $address->district;
        $this->city = $address->city;
        $this->postal_code = $address->postal_code;
        $this->delivery_notes = $address->delivery_notes;
        $this->is_default = $this->addressType === 'billing'
            ? $address->is_default_billing
            : $address->is_default_shipping;
    }

    private function resetForm()
    {
        $this->editingAddressId = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->phone = '';
        $this->email = '';
        $this->company_name = '';
        $this->tax_office = '';
        $this->tax_number = '';
        $this->address_line_1 = '';
        $this->address_line_2 = '';
        $this->neighborhood = '';
        $this->district = '';
        $this->city = '';
        $this->postal_code = '';
        $this->delivery_notes = '';
        $this->is_default = false;
    }

    public function render()
    {
        return view('shop::livewire.front.address-manager');
    }
}
