<?php

namespace App\Models\MercadoLibre;

use App\Models\Package;
use App\Presenters\CbtInvoicePresenter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class CbtInvoice
 * @package App\Models\MercadoLibre
 *
 * @property Package $package
 */
class CbtInvoice extends Model implements HasPresenter
{
    protected $table = 'mercadolibre_cbt_invoices';

    protected $fillable = ['package_id', 'invoice_url'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeOfPackageId($query, $id)
    {
        if (!empty($id) && is_array($id)) {
            return $query->whereIn("{$this->table}.package_id", $id);
        }
        return $id ? $query->where("{$this->table}.package_id", $id) : $query;
    }

    public function getPackageTrackingNumber()
    {
        return $this->package ? $this->package->tracking_number : null;
    }

    public function getPackageShipperName()
    {
        return $this->package ? $this->package->shipper : null;
    }

    public function getPackageAgreementClientName()
    {
        return $this->package ? $this->package->getClientName() : null;
    }

    public function getPackageAgreementServiceCode()
    {
        return $this->package ? $this->package->getAgreementServiceCode() : null;
    }

    public function getPackageAgreementServiceServiceTypeKey()
    {
        return $this->package ? $this->package->getAgreementServiceServiceTypeKey() : null;
    }

    public function getPackageAgreementServiceOriginLocationCountryName()
    {
        return $this->package ? $this->package->getAgreementServiceOriginLocationCountryName() : null;
    }

    public function getPackageAgreementServiceDestinationLocationCountryName()
    {
        return $this->package ? $this->package->getAgreementServiceDestinationLocationCountryName() : null;
    }

    public function getPackageBagDispatchAirWaybillCode()
    {
        return $this->package ? $this->package->getBagDispatchAirWaybillCode() : null;
    }

    public function scopeOfCreatedAtNewerThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return $query
            ->where('mercadolibre_cbt_invoices.created_at', '>=', $date . ' 23:59:59');
    }

    public function scopeOfCreatedAtOlderThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return $query
            ->where('mercadolibre_cbt_invoices.created_at', '<=', $date . ' 00:00:00');
    }

    public function getPresenterClass()
    {
        return CbtInvoicePresenter::class;
    }
}
