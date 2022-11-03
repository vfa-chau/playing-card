<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery as m;
use App\Services\PlayCardService;
use Illuminate\Support\Collection;

/**
 * @coversDefaultClass \App\Http\Controllers\PlayCardController
 */
class PlayCardTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers \App\Http\Requests\DistributeCardRequest
     */
    public function testDistributeWithParamEmpty()
    {
        $response = $this->post(route('card.distribute'));
        $response->assertSessionHasErrors([
            'number_of_player' => __('validation.custom.number_of_player.invalid')
        ]);
    }

    /**
     * @covers ::__construct
     * @covers \App\Http\Requests\DistributeCardRequest
     */
    public function testDistributeWithParamNotInt()
    {
        $response = $this->post(route('card.distribute'), [
            'number_of_player' => 'aa'
        ]);
        $response->assertSessionHasErrors([
            'number_of_player' => __('validation.custom.number_of_player.invalid')
        ]);
    }

    /**
     * @covers ::__construct
     * @covers \App\Http\Requests\DistributeCardRequest
     */
    public function testDistributeWithParamZero()
    {
        $response = $this->post(route('card.distribute'), [
            'number_of_player' => 0
        ]);
        $response->assertSessionHasErrors([
            'number_of_player' => __('validation.custom.number_of_player.invalid')
        ]);
    }

    /**
     * @covers ::__construct
     * @covers \App\Http\Requests\DistributeCardRequest
     */
    public function testDistributeWithParamMinusValue()
    {
        $response = $this->post(route('card.distribute'), [
            'number_of_player' => -1
        ]);
        $response->assertSessionHasErrors([
            'number_of_player' => __('validation.custom.number_of_player.invalid')
        ]);
    }

    /**
     * @covers ::__construct
     * @covers \App\Http\Requests\DistributeCardRequest
     */
    public function testDistributeWithParamDecimal()
    {
        $response = $this->post(route('card.distribute'), [
            'number_of_player' => 1.5
        ]);
        $response->assertSessionHasErrors([
            'number_of_player' => __('validation.custom.number_of_player.invalid')
        ]);
    }

    /**
     * @covers ::__construct
     * @covers \App\Http\Requests\DistributeCardRequest
     */
    public function testDistributeWithParamGreaterMax()
    {
        $response = $this->post(route('card.distribute'), [
            'number_of_player' => 100
        ]);
        $response->assertSessionHasErrors([
            'number_of_player' => __('validation.custom.number_of_player.invalid')
        ]);
    }

    /**
     * @covers ::__construct
     * @covers ::distribute
     * @covers \App\Http\Requests\DistributeCardRequest
     */
    public function testDistributeWithException()
    {
        $playCardService = m::mock(PlayCardService::class);
        $playCardService->shouldReceive('getDistributedCards')
            ->with(99)
            ->andThrow(new \Exception('Exception unit test.'));
        $this->app->instance(PlayCardService::class, $playCardService);

        $response = $this->post(route('card.distribute'), [
            'number_of_player' => 99
        ]);
        $response->assertSessionHasErrors([
            'error' => __('validation.custom.error')
        ]);
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith1Player()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 52;
        $params = [
            'number_of_player' => 1
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith2Players()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 26;
        $params = [
            'number_of_player' => 2
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith3Players()
    {
        $distributedCardLength = 51;
        $numberCardPerPerson = 17;
        $params = [
            'number_of_player' => 3
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(1, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith4Players()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 13;
        $params = [
            'number_of_player' => 4
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith5Players()
    {
        $distributedCardLength = 50;
        $numberCardPerPerson = 10;
        $params = [
            'number_of_player' => 5
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(2, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith6Players()
    {
        $distributedCardLength = 48;
        $numberCardPerPerson = 8;
        $params = [
            'number_of_player' => 6
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(4, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith7Players()
    {
        $distributedCardLength = 49;
        $numberCardPerPerson = 7;
        $params = [
            'number_of_player' => 7
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(3, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith8Players()
    {
        $distributedCardLength = 48;
        $numberCardPerPerson = 6;
        $params = [
            'number_of_player' => 8
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(4, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith9Players()
    {
        $distributedCardLength = 45;
        $numberCardPerPerson = 5;
        $params = [
            'number_of_player' => 9
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(7, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith10Players()
    {
        $distributedCardLength = 50;
        $numberCardPerPerson = 5;
        $params = [
            'number_of_player' => 10
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(2, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith11Players()
    {
        $distributedCardLength = 44;
        $numberCardPerPerson = 4;
        $params = [
            'number_of_player' => 11
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(8, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith12Players()
    {
        $distributedCardLength = 48;
        $numberCardPerPerson = 4;
        $params = [
            'number_of_player' => 12
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(4, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith13Players()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 4;
        $params = [
            'number_of_player' => 13
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith14Players()
    {
        $distributedCardLength = 42;
        $numberCardPerPerson = 3;
        $params = [
            'number_of_player' => 14
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(10, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith15Players()
    {
        $distributedCardLength = 45;
        $numberCardPerPerson = 3;
        $params = [
            'number_of_player' => 15
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(7, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith16Players()
    {
        $distributedCardLength = 48;
        $numberCardPerPerson = 3;
        $params = [
            'number_of_player' => 16
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(4, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith17Players()
    {
        $distributedCardLength = 51;
        $numberCardPerPerson = 3;
        $params = [
            'number_of_player' => 17
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(1, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith18Players()
    {
        $distributedCardLength = 36;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 18
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(16, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith19Players()
    {
        $distributedCardLength = 38;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 19
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(14, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith20Players()
    {
        $distributedCardLength = 40;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 20
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(12, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith21Players()
    {
        $distributedCardLength = 42;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 21
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(10, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith22Players()
    {
        $distributedCardLength = 44;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 22
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(8, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith23Players()
    {
        $distributedCardLength = 46;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 23
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(6, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith24Players()
    {
        $distributedCardLength = 48;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 24
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(4, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith25Players()
    {
        $distributedCardLength = 50;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 25
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(2, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith26Players()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 2;
        $params = [
            'number_of_player' => 26
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith27Players()
    {
        $distributedCardLength = 27;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 27
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(25, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith28Players()
    {
        $distributedCardLength = 28;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 28
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(24, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith29Players()
    {
        $distributedCardLength = 29;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 29
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(23, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith30Players()
    {
        $distributedCardLength = 30;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 30
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(22, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith31Players()
    {
        $distributedCardLength = 31;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 31
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(21, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith32Players()
    {
        $distributedCardLength = 32;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 32
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(20, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith33Players()
    {
        $distributedCardLength = 33;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 33
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(19, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith34Players()
    {
        $distributedCardLength = 34;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 34
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(18, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith35Players()
    {
        $distributedCardLength = 35;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 35
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(17, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith36Players()
    {
        $distributedCardLength = 36;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 36
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(16, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith37Players()
    {
        $distributedCardLength = 37;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 37
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(15, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith38Players()
    {
        $distributedCardLength = 38;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 38
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(14, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith39Players()
    {
        $distributedCardLength = 39;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 39
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(13, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith40Players()
    {
        $distributedCardLength = 40;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 40
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(12, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith41Players()
    {
        $distributedCardLength = 41;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 41
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(11, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith42Players()
    {
        $distributedCardLength = 42;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 42
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(10, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith43Players()
    {
        $distributedCardLength = 43;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 43
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(9, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith44Players()
    {
        $distributedCardLength = 44;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 44
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(8, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith45Players()
    {
        $distributedCardLength = 45;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 45
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(7, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith46Players()
    {
        $distributedCardLength = 46;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 46
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(6, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith47Players()
    {
        $distributedCardLength = 47;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 47
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(5, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith48Players()
    {
        $distributedCardLength = 48;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 48
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(4, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith49Players()
    {
        $distributedCardLength = 49;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 49
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(3, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith50Players()
    {
        $distributedCardLength = 50;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 50
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(2, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith51Players()
    {
        $distributedCardLength = 51;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 51
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(1, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith52Players()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 52
        ];
        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $this->getExpectedCards(
                $params['number_of_player'],
                $numberCardPerPerson
            ),
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith53Players()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 53
        ];
        $expectedCards = $this->getExpectedCards(
            $distributedCardLength,
            $numberCardPerPerson
        );
        array_push($expectedCards, [
            'person' => 53,
            'cardLength' => 0
        ]);

        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $expectedCards,
            $distributedCardLength
        );
    }

    /**
     * @covers ::distribute
     * @covers \App\Services\PlayCardService
     */
    public function testDistributeWith99Players()
    {
        $distributedCardLength = 52;
        $numberCardPerPerson = 1;
        $params = [
            'number_of_player' => 99
        ];
        $expectedCards = $this->getExpectedCards(
            $distributedCardLength,
            $numberCardPerPerson
        );
        for ($i = $distributedCardLength; $i < $params['number_of_player']; $i++) {
            array_push($expectedCards, [
                'person' => $i + 1,
                'cardLength' => 0
            ]);
        }

        $response = $this->post(route('card.distribute'), $params);
        $data = $response->getOriginalContent()->getData();
        $distributedCards = $data['distributedCards'];
        $this->assertEquals($distributedCardLength, $data['distributedCardTotal']);
        $this->assertEquals(0, $data['remainedCards']->count());
        $this->assertEquals($params['number_of_player'], $distributedCards->count());
        $this->assertDistributedCards(
            $distributedCards,
            $expectedCards,
            $distributedCardLength
        );
    }

    /**
     * Assert distributed cards
     */
    private function assertDistributedCards(
        Collection $distributedCards,
        array $expectedCards,
        int $maxCard
    ) {
        $allCards = collect([]);
        foreach ($distributedCards as $index => $item) {
            $expectedCard = $expectedCards[$index];
            $this->assertEquals($item['person'], $expectedCard['person']);
            $this->assertEquals($item['cards']->count(), $expectedCard['cardLength']);

            // Use to check duplicate card
            $allCards = $allCards->merge($item['cards']);
        }

        // Unique value
        $allCards = $allCards->unique();
        $this->assertEquals($allCards->count(), $maxCard);
    }

    /**
     * Get expected cards
     */
    private function getExpectedCards(
        int $numberOfPlayer,
        int $numberCardPerPerson
    ): array {
        $expectedCards = [];
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            array_push($expectedCards, [
                'person' => $i + 1,
                'cardLength' => $numberCardPerPerson
            ]);
        }

        return $expectedCards;
    }
}
