<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
        {--name= : The user name}
        {--email= : The user email address}
        {--role= : The role to assign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user and assign a role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Name')));
        $email = mb_strtolower(trim((string) ($this->option('email') ?: $this->ask('Email'))));

        $userValidator = Validator::make([
            'name' => $name,
            'email' => $email,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
        ]);

        if ($userValidator->fails()) {
            return $this->displayValidationErrors($userValidator->errors()->all());
        }

        $role = $this->resolveRole();

        if ($role === null) {
            $errMessage = 'Role must be one of: '.Role::query()->pluck('name')->implode(', ').'.';
            $this->error($errMessage);
            return self::FAILURE;
        }

        $password = $this->secret('Password');
        $passwordConfirmation = $this->secret('Confirm password');

        $passwordValidator = Validator::make([
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($passwordValidator->fails()) {
            return $this->displayValidationErrors($passwordValidator->errors()->all());
        }

        $user = DB::transaction(function () use ($email, $name, $password, $role): User {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $user->assignRole($role);

            return $user;
        });

        $this->info(sprintf(
            'User [%s] created with role [%s].',
            $user->email,
            $role->name,
        ));

        return self::SUCCESS;
    }

    private function resolveRole(): ?Role
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name')
            ->all();

        if ($roles === []) {
            $this->error('No roles found. Run the roles and permissions seeder first.');

            return null;
        }

        $roleName = trim((string) $this->option('role'));

        if ($roleName === '') {
            $defaultRole = array_search('manager', $roles, true);
            $roleName = $this->choice(
                'Role',
                $roles,
                $defaultRole === false ? 0 : $defaultRole,
            );
        }

        $role = Role::query()
            ->where('guard_name', 'web')
            ->where('name', $roleName)
            ->first();

        if ($role === null) {
            $this->error(sprintf('Role [%s] does not exist for the web guard.', $roleName));
        }

        return $role;
    }

    /**
     * @param  list<string>  $errors
     */
    private function displayValidationErrors(array $errors): int
    {
        foreach ($errors as $error) {
            $this->error($error);
        }

        return self::FAILURE;
    }
}
