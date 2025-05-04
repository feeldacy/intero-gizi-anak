<?php

    namespace App\Services;

    use App\Models\alamatTanah;
    use App\Models\detailTanah;
    use App\Models\fotoTanah;
    use App\Models\markerTanah;
    use App\Models\polygonTanah;
    use App\Models\sertifikatTanah;
    use App\Models\User;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
    use Haruncpi\LaravelIdGenerator\IdGenerator;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Storage;

    class UserService
    {
        public function storeUserData(array $data)
        {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        }

        public function updateUserData($id, array $data)
        {
            // kosong
        }

        public function deleteUserData($id)
        {
            // kosong
        }
    }


