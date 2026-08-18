<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserPasswordRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => [
                'nullable',
                'string',
                Rule::exists('roles', 'name')->where('guard_name', 'web'),
            ],
            'status' => ['nullable', Rule::enum(UserStatus::class)],
        ]);

        $users = User::query()
            ->with('roles')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $search = trim($search);

                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when(
                $filters['role'] ?? null,
                fn ($query, string $role) => $query->role($role),
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, string $status) => $query->where('status', $status),
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->roles(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => $this->roles(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->assignRole($data['role']);

            return $user;
        });

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User has been created.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => $this->roles(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $currentRole = $user->getRoleNames()->first();

        if ($request->user()->is($user) && $data['role'] !== $currentRole) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'You cannot change your own role.']);
        }

        if (
            $user->hasRole('admin')
            && $data['role'] !== 'admin'
            && ! User::role('admin')->where('id', '!=', $user->id)->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'The last administrator must keep the admin role.']);
        }

        DB::transaction(function () use ($data, $user): void {
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
            ])->saveOrFail();

            $user->syncRoles([$data['role']]);
        });

        return back()->with('status', 'User information has been updated.');
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user): RedirectResponse
    {
        $user->forceFill([
            'password' => $request->validated('password'),
        ])->saveOrFail();

        return back()->with('status', 'User password has been updated.');
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'user' => 'You cannot suspend your own account.',
            ]);
        }

        $user->suspend();

        return back()->with('status', 'User has been suspended.');
    }

    public function restore(User $user): RedirectResponse
    {
        $user->activate();

        return back()->with('status', 'User has been restored.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        if (
            $user->hasRole('admin')
            && ! User::role('admin')->where('id', '!=', $user->id)->exists()
        ) {
            return back()->withErrors([
                'user' => 'The last administrator cannot be deleted.',
            ]);
        }

        DB::transaction(function () use ($user): void {
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->delete();

            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            $user->deleteOrFail();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User has been permanently deleted.');
    }

    /**
     * @return Collection<int, Role>
     */
    private function roles(): Collection
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();
    }
}
