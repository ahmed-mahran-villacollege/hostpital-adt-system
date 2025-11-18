<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/


/**
 * Create a user seeded with the given permissions.
 */
function testUserWithPermissions(string ...$permissions): \App\Models\User
{
    $user = \App\Models\User::factory()->create();

    foreach ($permissions as $permission) {
        \Spatie\Permission\Models\Permission::findOrCreate($permission);
    }

    $user->givePermissionTo($permissions);

    return $user;
}

/**
 * Create a ward for testing.
 */
function testWard(string $type = 'Male', int $capacity = 1): \App\Models\Ward
{
    return \App\Models\Ward::create([
        'name' => 'Ward '.$type.' '.\Illuminate\Support\Str::random(4),
        'type' => $type,
        'capacity' => $capacity,
    ]);
}

/**
 * Create a team with an attached consultant.
 */
function testTeam(array $consultantAttributes = []): \App\Models\Team
{
    $consultant = \App\Models\Doctor::factory()->create(array_merge([
        'rank' => 'Consultant',
        'grade' => 5,
    ], $consultantAttributes));

    return \App\Models\Team::create([
        'name' => 'Team '.\Illuminate\Support\Str::random(4),
        'code' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(3)),
        'consultant_id' => $consultant->id,
    ]);
}
