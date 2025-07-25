<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run()
    {
        Task::create([
            'title' => 'Reunião com equipe',
            'description' => 'Discutir andamento do projeto e próximos passos',
            'due_date' => '2025-07-26',
            'completed' => true,
            'user_id' => 1,
        ]);
        Task::create([
            'title' => 'Enviar relatório',
            'description' => 'Enviar relatório semanal para o gestor',
            'due_date' => '2025-07-27',
            'completed' => false,
            'user_id' => 1,
        ]);
        Task::create([
            'title' => 'Reunião de alinhamento',
            'description' => 'Alinhar expectativas com o cliente',
            'due_date' => '2025-07-28',
            'completed' => false,
            'user_id' => 2,
        ]);
    }
} 