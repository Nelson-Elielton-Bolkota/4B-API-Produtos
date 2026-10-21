<?php

namespace Tests\Feature;

use App\Models\Produto;
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

    public function test_atualiza_produto_com_sucesso(): void
    {
        $produto = Produto::create([
            'nome' => 'Tijolo',
            'preco_custo' => 100,
            'preco' => 120,
        ]);

        $response = $this->putJson("/api/produtos/{$produto->id}", [
            'nome' => 'Tijolo Baiano',
            'preco' => 150,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id,
            'nome' => 'Tijolo Baiano',
            'preco' => 150,
        ]);
    }

    public function test_nao_atualiza_produto_com_preco_abaixo_da_margem_minima(): void
    {
        $produto = Produto::create([
            'nome' => 'Areia',
            'preco_custo' => 100,
            'preco' => 120,
        ]);

        $response = $this->putJson("/api/produtos/{$produto->id}", [
            'preco' => 105, 
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['preco']);

        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id,
            'preco' => 120,
        ]);
    }

}
