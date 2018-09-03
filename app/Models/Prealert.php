<?php

namespace App\Models;

use App\Presenters\PrealertPresenter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Package
 *
 * @package App
 * @property Package $package
 * @property string $request
 * @property string $response
 * @property boolean $success
 * @property string $errors
 * @property int $id
 * @property int $package_id
 * @property int $provider_id
 * @property string|null $reference
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Prealert extends Model implements HasPresenter
{
    protected $fillable = [
        'package_id',
        'provider_id',
        'request',
        'response',
        'success',
        'reference',
        'errors',
        'archived',
        'created_at'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function scopeOfPackageId($query, $package_id)
    {
        return !$package_id ? $query : $query->where('prealerts.package_id', $package_id);
    }

    public function scopeOfProviderId($query, $provider_id)
    {
        return !$provider_id ? $query : $query->where('prealerts.provider_id', $provider_id);
    }

    public function scopeOfSuccessful($query)
    {
        return $query->where('prealerts.success', true);
    }

    public function scopeOfUnsuccessful($query)
    {
        return $query->where('prealerts.success', false);
    }

    public function scopeOfArchived($query)
    {
        return $query->where('prealerts.archived', true);
    }

    public function scopeOfUnarchived($query)
    {
        return $query->where('prealerts.archived', false);
    }

    public function scopeOfTrackingNumber($query, $tn)
    {
        if (is_array($tn) && !empty($tn)) {
            $query->where(function ($q2) use ($tn) {
                collect($tn)->each(function ($item) use($q2){
                    $q2->orWhere('packages.tracking_number', strtoupper($item));
                });
            });
            return $query;
        } else {
            return !$tn ? $query : $query->where('packages.tracking_number', strtoupper($tn));
        }
    }

    public function scopeOfCreatedAtBeforeThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return !$date ? $query : $query->where('prealerts.created_at', '<=', $date . '  23:59:59');
    }

    public function scopeOfCreatedAtAfterThan($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return !$date ? $query : $query->where('prealerts.created_at', '>=', $date . ' 00:00:00');
    }

    public function setRequest($request)
    {
        $this->attributes['request'] = $request;
    }

    public function setResponse($response)
    {
        $this->attributes['response'] = $response;
    }

    public function setSuccess($success = true)
    {
        $this->attributes['success'] = $success;
    }

    public function setErrors($errors)
    {
        $this->attributes['errors'] = $errors;
    }

    public function getPackageTrackingNumber()
    {
        return $this->package ? $this->package->tracking_number : null;
    }

    public function getPackageAgreementClientName()
    {
        return $this->package ? $this->package->getClientName() : null;
    }

//    public function getPackageAgreementCountryName()
//    {
//        return $this->package ? $this->package->getAgreementCountryName() : null;
//    }

    public function getPackageAgreementServiceDestinationLocationCountryName()
    {
        return $this->package ? $this->package->getAgreementServiceDestinationLocationCountryName() : null;
    }

//    public function getPackageAgreementType()
//    {
//        return $this->package ? $this->package->getAgreementType() : null;
//    }

    public function getProviderName()
    {
        return $this->provider ? $this->provider->name : null;
    }

    public function getPresenterClass()
    {
        return PrealertPresenter::class;
    }
}