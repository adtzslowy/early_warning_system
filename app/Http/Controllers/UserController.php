<?php

declare(stric_types=1);

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query("q", ""));
        $role = (string) $request->query("role", "");

        $users = User::query()
            ->with("roles")
            ->when($search !== "", function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "like", "%{$seach}%")->orWhere(
                        "email",
                        "like",
                        "%{$seach}%",
                    );
                });
            })
            ->when($role !== "", function ($query) use ($role) {
                $query->whereHas("roles", fn($q) => $q->where("name", $role));
            })
            ->orderBy("name")
            ->paginate(10)
            ->withQueryString();

        return view("users.index", [
            "users" => $users,
            "search" => $search,
            "role" => $role,
            "roles" => Role::orderBy("name")->get(),
            "total" => User::count(),
        ]);
    }

    public function create()
    {
        return view("users.create", [
            "roles" => Role::orderBy("name")->get(),
            "devices" => Device::orderBy("name")->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $user = User::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => $data["password"],
        ]);

        $user->syncRoles($data["role"]);
        $user->devices()->sync($this->deviceIdsFor($data));

        return redirect()
            ->route("users")
            ->with("status", "User {$user->name} berhasil ditambahkan");
    }

    public function edit(User $user)
    {
        $user->load("devices");

        return view("users.edit", [
            "user" => $user,
            "roles" => Role::orderBy("name")->get(),
            "devices" => Device::orderBy("name")->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        $user->name = $data["name"];
        $user->email = $data["email"];

        if (!empty($data["password"])) {
            $user->password = $data["password"];
        }

        $user->save();
        $user->syncRoles($data["role"]);
        $user->devices()->sync($this->deviceIdsFor($data));

        return redirect()
            ->route("users")
            ->with("status", "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return redirect()
                ->route("users")
                ->with(
                    "status",
                    "Anda tidak dapat menghapus akun anda sendiri",
                );
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route("users")
            ->with("status", "User {$name} berhasil dihapus.");
    }

    private function validated(Request $request, ?User $user = null)
    {
        return $request->validate(
            [
                "name" => ["required", "string", "max:255"],
                "email" => [
                    "required",
                    "string",
                    "email",
                    "max:255",
                    Rule::unique("users", "email")->ignore($user?->id),
                ],
                "role" => ["required", Rule::exists("roles", "name")],
                "password" => [
                    $user ? "nullable" : "required",
                    "confirmed",
                    Password::min(8),
                ],
                "devices" => ["array"],
                "devices.*" => ["exists:devices,id"],
            ],
            [],
            [
                "name" => "nama",
                "email" => "email",
                "role" => "role",
                "password" => "kata sandi",
            ],
        );
    }

    private function deviceIdsFor(array $data): array
    {
        return ($data["role"] ?? null) === "operator"
            ? $data["devices"] ?? []
            : [];
    }
}
