<?php
/**
 * Created by Henry Leon.
 * User: developer
 * Date: 30/05/18
 * Time: 08:18 AM
 */

namespace App\Repositories;

use App\Models\ProviderInvoice;
use Illuminate\Support\Collection;

class ProviderInvoiceRepository extends AbstractRepository
{
    function __construct(ProviderInvoice $model)
    {
        $this->model = $model;
    }

    public function search(array $params = [], $distinct = true)
    {
        $query = $this->model;
        $joins = collect();

        $query = $this->model
            ->select('provider_invoices.*')
            ->distinct();

        if ($distinct) {
            $query = $query->distinct();
        }

        if (isset($params['number']) && $params['number']) {
            $query->where('number', '=', $params['number']);
        }

        if (isset($params['provider_id'])) {
            $query->where('provider_id', '=', $params['provider_id']);
        }

        if (isset($params['amount'])) {
            $query->where('amount', '=', $params['amount']);
        }

        if (isset($params['invoiced_at'])) {
            $query->where('invoiced_at', '=', $params['invoiced_at']);
        }

        if (isset($params['currency_id'])) {
            $query->where('currency_id', '=', $params['currency_id']);
        }

        return $query;
    }

    public function attachPackages(ProviderInvoice $providerInvoice, Collection $packages, $amount)
    {
        $providerInvoice->packages()->attach($packages->pluck('id')->toArray(), ['amount' => $amount]);

        return $providerInvoice->save();
    }

    public function detachPackages(ProviderInvoice $providerInvoice, Collection $packages)
    {
        return $providerInvoice->packages()->detach($packages->pluck('id')->toArray());
    }
}