<?php

namespace App\Domains\ChangeLog\Action;

use App\Domains\ChangeLog\Entity\ChangeLogItem;
use App\Domains\ChangeLog\Service\ChangeLogRetriever\ChangeLogRetrieverInterface;
use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\Shared\Http\AbstractAction;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetChangeLogListAction extends AbstractAction
{
    /** @var ChangeLogRetrieverInterface */
    private $changeLogRetriever;

    public function __construct(Request $request, ChangeLogRetrieverInterface $changeLogRetriever)
    {
        parent::__construct($request);

        $this->changeLogRetriever = $changeLogRetriever;
    }

    public function listChangeLog(TokenInfo $tokenInfo): JsonResponse
    {
        $startId = intval($this->getRequest()->get('start_id', 0));

        if ($startId > 0) {
            $startId = new ChangeLogId($startId);
        } else {
            $startId = null;
        }

        $list = $this->changeLogRetriever->getChangeLog($tokenInfo->getUserId(), $startId);

        $results = array_map(function(ChangeLogItem $changeLogItem) {
            return $changeLogItem->toArray();
        }, $list->toArray());

        $this->setResponseData($results);
        return $this->getResponse();
    }
}
