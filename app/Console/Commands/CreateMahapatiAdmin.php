<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('mahapati:make-admin')]
#[Description('Create the first MAHAPATI administrator account.')]
class CreateMahapatiAdmin extends Command
{
    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama lengkap Admin'));
        $email = Str::lower(trim((string) $this->ask('Email Admin')));
        $password = (string) $this->secret('Password Admin (minimal 12 karakter)');
        $passwordConfirmation = (string) $this->secret('Ulangi password Admin');

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Nama dan alamat email yang valid wajib diisi.');

            return self::FAILURE;
        }

        if (strlen($password) < 12 || $password !== $passwordConfirmation) {
            $this->error('Password harus minimal 12 karakter dan kedua isian harus sama.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('Email tersebut sudah digunakan akun lain.');

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info('Akun Admin berhasil dibuat. Password tidak ditampilkan kembali.');

        return self::SUCCESS;
    }
}
