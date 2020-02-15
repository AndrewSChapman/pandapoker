<?php


namespace App\Http\Middleware;


use App\Domains\Shared\Http\AuthenticatedRequest;
use App\Domains\Shared\Security\SecuritySingleton;
use App\Domains\Shared\Security\Service\TokenService\TokenServiceInterface;
use App\Domains\Shared\Security\Service\TokenService\Type\Token;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthMiddleware
{
    /** @var TokenServiceInterface */
    private $tokenService;

    /**
     * TokenAuthMiddleware constructor.
     * @param TokenServiceInterface $tokenService
     */
    public function __construct(TokenServiceInterface $tokenService)
    {
        $this->tokenService = $tokenService;
    }


    public function handle(Request $request, \Closure $next)
    {
        $bearerToken = $request->bearerToken();
        if (empty($bearerToken)) {
            return $this->getInvalidBearerTokenResponse();
        }

        $tokenInfo = $this->tokenService->verifyToken(new Token($bearerToken));
        if (!$tokenInfo) {
            return $this->getInvalidBearerTokenResponse();
        }

        SecuritySingleton::setTokenInfo($tokenInfo);

        return $next($request);
    }

    private function getInvalidBearerTokenResponse(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Invalid Bearer Token'
        ], Response::HTTP_FORBIDDEN);
    }
}
