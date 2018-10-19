<?PHP
namespace Api\Tests;
use PHPUnit\Framework\TestCase;
use Api\Core\RouteModel;

final class RoutesTest extends TestCase
{
    private $routes;

    function __construct() {
        $this->routes = RouteModel::getRoutes();
        $this->withoutEvents();
    }

    public function testGetShouldReturnArrayOrNotImplementedException(): void
    {
        foreach ($this->routes as $route) {
            $routeInstance = new ucfirst($route);
            $this->assert(
                $route::get()
            );
        }
    }

    public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }
}