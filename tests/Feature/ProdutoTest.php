<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProdutoTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase; // Faltou essa trait na aula -> serve pra utilizar e dar refresh na memória temporária do sqlite


    public function test_cria_produto_com_sucesso(): void
    {
        $this->postJson('/api/produtos', [
            'nome' => 'Lajota', 
            'preco_custo' => 100,
            'preco' => 110,
        ])->assertCreated();

        $this->assertDatabaseHas('produtos', [
            'nome' => 'Lajota']);
    }

    public function test_nao_cria_produto_com_preco_abaixo_da_margem_minima(): void
    {
        $response = $this->postJson('/api/produtos', [
            'nome' => 'Cimento',
            'preco_custo' => 100,
            'preco' => 105,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['preco']);

        $this->assertDatabaseMissing('produtos', [
            'nome' => 'Cimento',
        ]);
    }
}
