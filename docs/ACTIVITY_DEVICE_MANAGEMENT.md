# Activity Diagram Manajemen Data Alat

Diagram aktivitas berikut menggambarkan proses manajemen data alat (Device), meliputi:
- Tambah Data Alat (Create)
- Ubah Data Alat (Update)  
- Hapus Data Alat (Delete)

---

## a. Activity Diagram Tambah Data Alat

```
START
  ↓
┌────────────────────────────────────┐
│ User: Admin/Operator               │
│ Click "Tambah Alat"                │
└────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ System: Check Permission                       │
│ (permission:view devices required)             │
└────────────────────────────────────────────────┘
  ↓
      ┌─── PERMISSION DENIED ───┐
      │                         ↓
      │         Redirect to 403/Login
      │                         ↓
      │           END (Akses ditolak)
      │
  ↓
┌────────────────────────────────────────────────┐
│ System: GET /devices/create                    │
│ DeviceController::create()                     │
│ Return view('device.create') +                 │
│ StatusEnum (online/offline/maintenance)        │
└────────────────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ UI: Display Form Tambah Alat                   │
│ Fields:                                        │
│ - Kode Device (required, alphanumeric+dash)   │
│ - Nama Alat (required, max 255 char)          │
│ - Lokasi (optional)                           │
│ - Latitude (optional, -90 to 90)              │
│ - Longitude (optional, -180 to 180)           │
│ - API URL (optional, valid URL)               │
│ - API Key (optional)                          │
│ - API Enabled (checkbox)                      │
│ - Status (required: online/offline)           │
└────────────────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ User: Input data alat + Click "Simpan"        │
└────────────────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ System: POST /devices                          │
│ DeviceController::store()                      │
└────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ VALIDASI INPUT:                                      │
│ ✓ device_code:                                       │
│   - required, max 64 char                            │
│   - regex: ^[A-Za-z0-9_-]+$ (alphanumeric + dash)   │
│   - unique (tidak boleh duplikat di DB)              │
│ ✓ name: required, max 255 char                       │
│ ✓ location: optional, max 255 char                   │
│ ✓ latitude: numeric, between -90 to 90              │
│ ✓ longitude: numeric, between -180 to 180          │
│ ✓ api_url: valid URL format                          │
│ ✓ api_key: max 255 char                              │
│ ✓ status: enum (online/offline/maintenance)         │
└──────────────────────────────────────────────────────┘
  ↓
      ┌─── VALIDASI GAGAL ───┐
      │                      ↓
      │  Return to form with error messages
      │  Display field yang error                 
      │                      ↓
      │     User correct input & resubmit
      │                      ↓
      │         Back to VALIDASI INPUT
      │
  ↓
┌──────────────────────────────────────────────────────┐
│ VALIDASI BERHASIL → CREATE DEVICE                    │
│ Device::create([                                     │
│   'device_code' => input,                            │
│   'name' => input,                                   │
│   'location' => input,                               │
│   'latitude' => input,                               │
│   'longitude' => input,                              │
│   'api_url' => input,                                │
│   'api_key' => input (jika ada),                     │
│   'api_enabled' => boolean,                          │
│   'status' => input,                                 │
│   'last_seen_at' => NULL (baru dibuat)              │
│ ])                                                   │
│                                                      │
│ → Device record di-insert ke database                │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ ASSIGN DEVICE OWNERSHIP                              │
│ $request->user()->devices()->attach($device->id)    │
│ → Link device ke user (many-to-many relationship)   │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ REDIRECT to /devices                                 │
│ with session message:                                │
│ "Device {device_code} berhasil ditambahkan."        │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ UI: Display Device List                              │
│ - Show success message di top                        │
│ - New device appear di table                         │
│ - Ready untuk di-edit/delete atau polling data      │
└──────────────────────────────────────────────────────┘
  ↓
END (Berhasil)
```

---

## b. Activity Diagram Ubah Data Alat

```
START
  ↓
┌────────────────────────────────────┐
│ User: Admin/Operator               │
│ Click Edit pada device list        │
│ atau direct ke /devices/{id}/edit   │
└────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ System: Check Permission                       │
│ (permission:view devices required)             │
└────────────────────────────────────────────────┘
  ↓
      ┌─── PERMISSION DENIED ───┐
      │                         ↓
      │         Redirect to 403/Login
      │                         ↓
      │           END (Akses ditolak)
      │
  ↓
┌────────────────────────────────────────────────┐
│ System: GET /devices/{device:device_code}/edit │
│ DeviceController::edit($device)                │
│ Retrieve device from DB by device_code         │
└────────────────────────────────────────────────┘
  ↓
      ┌─── DEVICE NOT FOUND ───┐
      │                        ↓
      │    Throw 404 ModelNotFoundException
      │                        ↓
      │       END (Device tidak ditemukan)
      │
  ↓
┌────────────────────────────────────────────────┐
│ System: Return edit form pre-populated          │
│ view('device.edit') dengan:                     │
│ - Current device data (dari DB)                 │
│ - StatusEnum options                           │
└────────────────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ UI: Display Form Edit Alat                      │
│ Fields (sama dengan Create, tapi sudah terisi):│
│ - Kode Device (read-only atau disabled)        │
│ - Nama Alat (current value)                    │
│ - Lokasi (current value)                       │
│ - Latitude (current value)                     │
│ - Longitude (current value)                    │
│ - API URL (current value)                      │
│ - API Key (current value)                      │
│ - API Enabled (current toggle state)           │
│ - Status (current status selected)             │
└────────────────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ User: Edit field yang ingin diubah +           │
│ Click "Simpan"                                 │
└────────────────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ System: PATCH /devices/{device:device_code}    │
│ DeviceController::update()                     │
└────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ VALIDASI INPUT (sama seperti Create):               │
│ ✓ device_code: unique IGNORE own record             │
│ ✓ name: required, max 255                           │
│ ✓ location, latitude, longitude: optional           │
│ ✓ api_url, api_key: optional                        │
│ ✓ status: enum validation                           │
└──────────────────────────────────────────────────────┘
  ↓
      ┌─── VALIDASI GAGAL ───┐
      │                      ↓
      │  Return to form with error messages
      │                      ↓
      │     User correct & resubmit
      │
  ↓
┌──────────────────────────────────────────────────────┐
│ VALIDASI BERHASIL → UPDATE DEVICE                    │
│ $device->update([                                    │
│   'device_code' => input,                            │
│   'name' => input,                                   │
│   ... (updated fields)                               │
│ ])                                                   │
│                                                      │
│ → Only update fields yang changed (PATCH behavior)  │
│ → Updated_at timestamp di-update otomatis           │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ REDIRECT to /devices                                 │
│ with session message:                                │
│ "Device {device_code} berhasil diperbarui."         │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ UI: Display Device List                              │
│ - Show success message                               │
│ - Device row reflect perubahan data terbaru          │
│ - RobSyncService akan pick up changes next poll      │
└──────────────────────────────────────────────────────┘
  ↓
END (Berhasil)
```

---

## c. Activity Diagram Hapus Data Alat

```
START
  ↓
┌────────────────────────────────────┐
│ User: Admin/Operator               │
│ Click Delete/Trash icon pada row   │
│ Confirm: "Yakin hapus?"            │
└────────────────────────────────────┘
  ↓
┌────────────────────────────────────────────────┐
│ System: Check Permission                       │
│ (permission:view devices required)             │
└────────────────────────────────────────────────┘
  ↓
      ┌─── PERMISSION DENIED ───┐
      │                         ↓
      │         Redirect to 403/Login
      │                         ↓
      │           END (Akses ditolak)
      │
  ↓
┌────────────────────────────────────────────────┐
│ System: DELETE /devices/{device:device_code}   │
│ DeviceController::destroy($device)             │
│ Route model binding retrieve device            │
└────────────────────────────────────────────────┘
  ↓
      ┌─── DEVICE NOT FOUND ───┐
      │                        ↓
      │    Throw 404 ModelNotFoundException
      │                        ↓
      │       END (Device tidak ditemukan)
      │
  ↓
┌──────────────────────────────────────────────────────┐
│ STORE device_code untuk session message              │
│ $code = $device->device_code                         │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ DELETE DEVICE & RELATED DATA:                        │
│                                                      │
│ $device->delete()                                    │
│                                                      │
│ Cascade delete (Laravel):                            │
│ ✓ Device record → deleted                            │
│ ✓ Sensors (device_id FK) → deleted otomatis         │
│ ✓ RiskEvaluations → deleted otomatis                 │
│ ✓ Predictions → deleted otomatis                     │
│ ✓ Alerts → deleted otomatis                          │
│ ✓ user_devices pivot → deleted otomatis             │
│                                                      │
│ Note: All cascade configured di model relationships  │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ REDIRECT to /devices                                 │
│ with session message:                                │
│ "Device {code} berhasil dihapus."                   │
└──────────────────────────────────────────────────────┘
  ↓
┌──────────────────────────────────────────────────────┐
│ UI: Display Device List (tanpa device yang dihapus)  │
│ - Show success message                               │
│ - Row device hilang dari table                       │
│ - Pagination adjust jika perlu                       │
└──────────────────────────────────────────────────────┘
  ↓
END (Berhasil)
```

---

## 🔐 Mekanisme Ownership Device (Many-to-Many Relationship)

### Diagram Ownership Flow (saat Create Device):

```
User (Admin/Operator)
        ↓
   Click "Tambah Alat"
        ↓
   Device::create() → Buat record device baru
        ↓
   $request->user()->devices()->attach($device->id)
        ↓
   ┌─────────────────────────────────────────────────────┐
   │ PIVOT TABLE: user_device (Junction Table)           │
   ├─────────────────────────────────────────────────────┤
   │ user_id  │ device_id  │ created_at │ updated_at    │
   ├──────────┼────────────┼────────────┼───────────────┤
   │ 5        │ abc123     │ 2026-07-17 │ 2026-07-17   │
   └─────────────────────────────────────────────────────┘
        ↓
   Device sekarang "milik" User ID 5
```

### Implementasi di Code:

**Device Model:**
```php
public function operators(): BelongsToMany
{
    return $this->belongsToMany(User::class)->withTimestamps();
}
```

**DeviceController::store():**
```php
$device = Device::create($data);                    // 1. Buat device
$request->user()->devices()->attach($device->id);  // 2. Link ke user (owner)
```

### Database Schema:

**Tabel devices:**
```
id | device_code | name | location | latitude | longitude | status | last_seen_at
```

**Tabel users:**
```
id | name | email | password | email_verified_at | role
```

**Tabel user_device (Pivot/Junction Table):**
```
user_id | device_id | created_at | updated_at
```

### Access Control dengan Ownership:

Saat user membuka list devices, sistem menggunakan **scope visibleTo**:

```php
// Device Model
public function scopeVisibleTo(Builder $query, User $user): Builder
{
    if ($user->hasRole("admin")) {
        return $query;  // Admin bisa lihat SEMUA device
    }

    // Non-admin hanya lihat device yang mereka own
    return $query->whereHas("operators", 
        fn($q) => $q->whereKey($user->id)
    );
}
```

### Contoh Query:

**Query untuk Admin:**
```sql
SELECT * FROM devices;
-- Result: Semua device (tidak ada filter)
```

**Query untuk Operator (User ID = 5):**
```sql
SELECT d.* FROM devices d
WHERE EXISTS (
    SELECT 1 FROM user_device ud
    WHERE ud.device_id = d.id 
    AND ud.user_id = 5
);
-- Result: Hanya device yang di-own oleh user 5
```

### Lifecycle Ownership:

```
1. USER A create device "ROB-KTP-01"
   ↓
   user_device pivot: (user_a_id, rob-ktp-01_id)
   
2. USER A bisa EDIT device sendiri ✓
   
3. USER B (bukan owner) mau edit "ROB-KTP-01"
   ↓
   System: Check ownership di visibleTo scope
   ↓
   403 Forbidden (User B tidak punya akses)
   
4. ADMIN ingin USER B dapat akses "ROB-KTP-01"
   ↓
   Admin: $device->operators()->attach($user_b_id)
   ↓
   Sekarang user_device pivot punya 2 rows:
   - (user_a_id, rob-ktp-01_id)
   - (user_b_id, rob-ktp-01_id)
   ↓
   USER B sekarang bisa lihat & edit device ✓
   
5. Admin ingin revoke akses USER B
   ↓
   Admin: $device->operators()->detach($user_b_id)
   ↓
   Pivot row (user_b_id, rob-ktp-01_id) dihapus
   ↓
   USER B tidak bisa lihat device lagi
```

### Perbedaan Admin vs Operator:

| Aspek | Admin | Operator |
|-------|-------|----------|
| **Lihat device** | Semua device (via scope tidak ada filter) | Hanya device yang di-own (via scope whereHas) |
| **Create device** | Ya, ownership otomatis → attach ke diri sendiri | Ya, ownership otomatis → attach ke diri sendiri |
| **Edit device** | Bisa edit semua | Hanya bisa edit device sendiri |
| **Delete device** | Bisa hapus semua | Hanya bisa hapus device sendiri |
| **Share device** | Bisa attach user lain ke device via pivot | Tidak bisa (permission:edit devices di-enforce) |
| **Permission** | permission:view devices (allow all CRUD) | permission:view devices (allow CRUD sendiri) |

### Modifikasi Ownership (Admin Feature):

Meskipun tidak ada UI untuk ini, admin bisa share device ke user lain via:

```php
// Admin share device ke user lain
$device->operators()->attach($user_id);  // Add user ke device

// Admin revoke access
$device->operators()->detach($user_id);  // Remove user dari device

// Admin ganti ownership primary
$device->operators()->sync([$new_owner_id]);  // Set exclusive owner
```

---

## Ringkasan: Device Management CRUD Flow

| Operation | HTTP Method | Endpoint | Controller | Key Process |
|-----------|-------------|----------|-----------|-------------|
| **List** | GET | `/devices` | index() | Show paginated list, search, filter by status |
| **Create (Form)** | GET | `/devices/create` | create() | Display empty form |
| **Create (Save)** | POST | `/devices` | store() | Validate → Insert → Attach to user → Redirect |
| **Edit (Form)** | GET | `/devices/{device_code}/edit` | edit() | Display pre-populated form |
| **Edit (Save)** | PATCH | `/devices/{device_code}` | update() | Validate → Update → Redirect |
| **Delete** | DELETE | `/devices/{device_code}` | destroy() | Delete → Cascade delete related → Redirect |

---

## Validasi Rules (Shared untuk Create & Update)

```php
[
    'device_code' => [
        'required',
        'string',
        'max:64',
        'regex:/^[A-Za-z0-9_-]+$/',
        Rule::unique('devices', 'device_code')->ignore($device?->id)
    ],
    'name' => ['required', 'string', 'max:255'],
    'location' => ['nullable', 'string', 'max:255'],
    'latitude' => ['nullable', 'numeric', 'between:-90,90'],
    'longitude' => ['nullable', 'numeric', 'between:-180,180'],
    'api_url' => ['nullable', 'url', 'max:2048'],
    'api_key' => ['nullable', 'string', 'max:255'],
    'api_enabled' => ['boolean'],
    'status' => ['required', Rule::enum(DeviceStatus::class)]
]
```

---

## Permission Guard

Semua route dalam device management dilindungi:
```php
->middleware('permission:view devices')
```

User harus memiliki permission `view devices` untuk:
- Melihat list
- Membuat device baru
- Edit device
- Delete device

Role yang punya permission ini: Admin, Operator (default)

---

## Database Cascading

Saat device dihapus, relasi berikut juga auto-delete (foreign key constraint dengan ON DELETE CASCADE):

- **Sensors** (device_id) → Semua pembacaan sensor terhapus
- **RiskEvaluations** (device_id) → Semua evaluasi risiko terhapus
- **Predictions** (device_id) → Semua prediksi terhapus
- **Alerts** (device_id) → Semua alert history terhapus
- **user_devices** (device_id) → Hubungan user-device terhapus

Ini memastikan data consistency: tidak ada orphan records di DB.

---

**Selesai!** Tinggal di-insert ke laporan Anda. Mau saya bikin visual diagram (mermaid/PNG) juga? 📊
