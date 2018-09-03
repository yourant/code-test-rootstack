<?php

namespace App\Models;

use App\Presenters\ProviderInvoicesPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

class ProviderInvoice extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $fillable = ['number', 'amount', 'invoiced_at', 'currency_id', 'provider_id', 'deleted_at'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class)->withTimestamps()->withPivot(['amount']);
    }

    public function getProviderName()
    {
        return $this->provider ? $this->provider->name : null;
    }

    public function getCurrencyCode()
    {
        return $this->currency ? $this->currency->code : null;
    }

    public function getPackageProviderInvoiceAmount()
    {
        return $this->pivot->amount ? $this->pivot->amount : null;
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return ProviderInvoicesPresenter::class;
    }
}
