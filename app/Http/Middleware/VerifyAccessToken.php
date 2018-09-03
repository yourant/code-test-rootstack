<?php
namespace App\Http\Middleware;

use App\Repositories\ClientRepository;
use App\Repositories\TrackerRepository;
use App\Traits\JsonResponse;
use Closure;

class VerifyAccessToken
{

    use JsonResponse;

    /**
     * @var ClientRepository
     */
    protected $client;

    /**
     * @var TrackerRepository
     */
    protected $tracker;

    public function __construct(ClientRepository $client, TrackerRepository $tracker)
    {
        $this->client = $client;
        $this->tracker = $tracker;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$access_token = $request->input('access_token')) {
            return self::errorResponse('Unauthorized action', 403);
        }

        // Search access_token in TrackerRepository
        if ($t = $this->tracker->getByAccessToken($access_token)) {
            return $next($request);
        }

        // Search access_token in ClientRepository
        if ($c = $this->client->search(compact('access_token'))->first()) {
            return $next($request);
        }

        return self::errorResponse('Unauthorized action', 403);
    }

}