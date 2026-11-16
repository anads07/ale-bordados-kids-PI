<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            [
                'nome_produto' => 'Fraldinha de Boca',
                'categoria' => 'Fraldas',
                'preco' => 25.00,
                'estoque' => 10,
                'descricao' => 'Pequena, delicada e indispensável! Perfeita para acompanhar o bebê nas mamadas, passeios e no dia a dia, trazendo praticidade e carinho em cada detalhe.',
                'imagem' => 'fraldinha-boca.jpeg',
            ],
            [
                'nome_produto' => 'Fralda',
                'categoria' => 'Fraldas',
                'preco' => 45.00,
                'estoque' => 10,
                'descricao' => 'Fralda macia e confortável, ideal para o dia a dia do bebê. Uma peça prática e delicada para completar o enxoval com carinho.',
                'imagem' => 'fralda.jpeg',
            ],
            [
                'nome_produto' => 'Toalhinha',
                'categoria' => 'Toalhas',
                'preco' => 40.00,
                'estoque' => 10,
                'descricao' => 'Uma peça versátil e delicada, ideal para os cuidados diários do bebê. Prática para levar na bolsa e ter sempre à mão!',
                'imagem' => 'toalhinha.jpeg',
            ],
            [
                'nome_produto' => 'Body',
                'categoria' => 'Roupas',
                'preco' => 45.00,
                'estoque' => 10,
                'descricao' => 'Conforto e fofura para os primeiros momentos do bebê! Uma peça essencial no enxoval, perfeita para deixar o pequeno ainda mais charmoso.',
                'imagem' => 'body.jpeg',
            ],
            [
                'nome_produto' => 'Toalha de Rosto',
                'categoria' => 'Toalhas',
                'preco' => 60.00,
                'estoque' => 10,
                'descricao' => 'Delicadeza e praticidade para os cuidados diários. Ideal para deixar a rotina do bebê ainda mais confortável e especial.',
                'imagem' => 'toalha-rosto.jpeg',
            ],
            [
                'nome_produto' => 'Kit Fralda (Fralda grande + Fralda pequena)',
                'categoria' => 'Kits',
                'preco' => 70.00,
                'estoque' => 10,
                'descricao' => 'A combinação perfeita de praticidade e carinho! Duas peças essenciais para acompanhar o bebê em diferentes situações, tornando a rotina muito mais fácil.',
                'imagem' => 'kit-fralda.jpeg',
            ],
            [
                'nome_produto' => 'Body + Calça',
                'categoria' => 'Conjuntos',
                'preco' => 70.00,
                'estoque' => 10,
                'descricao' => 'Um conjuntinho cheio de charme e conforto! Perfeito para vestir o bebê com delicadeza, praticidade e muito carinho.',
                'imagem' => 'body-calca.jpeg',
            ],
            [
                'nome_produto' => 'Toalha de Rosto (Luxo)',
                'categoria' => 'Toalhas',
                'preco' => 100.00,
                'estoque' => 10,
                'descricao' => 'Um momento de aconchego depois do banho! Ideal para envolver o bebê com conforto e proporcionar uma experiência ainda mais gostosa na hora dos cuidados.',
                'imagem' => 'toalha-banho.jpeg',
            ],
            [
                'nome_produto' => 'Toalha de Banho com Capuz (Bebê)',
                'categoria' => 'Toalhas',
                'preco' => 60.00,
                'estoque' => 10,
                'descricao' => 'A combinação perfeita de conforto, proteção e fofura! Com capuz para manter a cabecinha do bebê aquecida após o banho, é uma peça especial e indispensável no enxoval.',
                'imagem' => 'toalha-capuz.jpeg',
            ],
        ];

        foreach ($produtos as $produto) {
            DB::table('produtos')->updateOrInsert(
                ['nome_produto' => $produto['nome_produto']],
                $produto
            );
        }
    }
}
