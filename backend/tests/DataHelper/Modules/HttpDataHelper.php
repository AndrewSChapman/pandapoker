<?php

namespace Testing\DataHelper\Modules;

use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\MockObject;

class HttpDataHelper extends AbstractDataHelperModule
{
    /**
     * @param $postData array|null
     * @return Request|MockObject
     */
    public function makeRequest(array $postData = null): Request
    {
        $request = $this->getTestCase()->getMockBuilder(Request::class)
         ->disableOriginalConstructor()
         ->getMock();

        // If postData has been provided, setup the request object all and post methods to return it.
        if (!is_null($postData)) {
            $numPostParams = count($postData);

            // Define the POST parameter behaviour
            $request->expects($this->getTestCase()->once())->method('all')->willReturn($postData);

            // We expect calls for each POST parameter, and we need to tell PHPUnit what will be returned by each post param name.
            $request->expects($this->getTestCase()->exactly($numPostParams))->method('post')
                ->with($this->getTestCase()->callback(function($paramName) use ($postData) {
                    return isset($postData[$paramName]);
                }
            ))->willReturnCallback(function(string $paramName) use ($postData) {
                return $postData[$paramName];
            });
        }

        return $request;
    }
}
