<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        if ($users->count() > 0) {
            Task::create([
                'title' => 'Reunião com equipe',
                'description' => 'Discutir andamento do projeto e próximos passos',
                'due_date' => '2025-07-26',
                'completed' => true,
                'user_id' => $users->first()->id,
            ]);

            Task::create([
                'title' => 'Enviar relatório',
                'description' => 'Enviar relatório semanal para o gestor',
                'due_date' => '2025-07-27',
                'completed' => false,
                'user_id' => $users->first()->id,
            ]);

            if ($users->count() > 1) {
                Task::create([
                    'title' => 'Reunião de alinhamento',
                    'description' => 'Alinhar expectativas com o cliente',
                    'due_date' => '2025-07-28',
                    'completed' => false,
                    'user_id' => $users->get(1)->id,
                ]);
            }
        }
    }
} 