<?php

namespace AppBundle\Tests\Controller\API;

use AppBundle\Tests\Controller\BaseControllerTestCase;

class CityControllerTest extends BaseControllerTestCase
{
    public function testGetCitiesList()
    {
        $cities = [];
        foreach ($this->fixtures as $key => $fixture) {
            if (strpos($key, 'city__') == 0) {
                $cities[] = $fixture;
            }
        }

        $response = $this->request('/api/mobile/v1/cities');
        $this->assertStatusCode(200, $this->client);

        $serializer = $this->getContainer()->get('jms_serializer');
        $content = $serializer->serialize([
            'data' => $cities,
            'status' => 'Success',
            'errors' => [],
        ], 'json');
        $this->assertEquals($content, $response->getContent());
    }
}
