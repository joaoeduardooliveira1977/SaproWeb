<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{Fase, GrauRisco, TipoAcao, TipoProcesso, Assunto, Reparticao, Secretaria, IndiceMonetario, Pessoa, Usuario};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Fases ──────────────────────────────────────
        $fases = [
            ['codigo' => 'INI', 'descricao' => 'Inicial',        'ordem' => 1],
            ['codigo' => 'CON', 'descricao' => 'Conhecimento',    'ordem' => 2],
            ['codigo' => 'INS', 'descricao' => 'Instrução',       'ordem' => 3],
            ['codigo' => 'SEN', 'descricao' => 'Sentença',        'ordem' => 4],
            ['codigo' => 'REC', 'descricao' => 'Recursal',        'ordem' => 5],
            ['codigo' => 'CUM', 'descricao' => 'Cumprimento',     'ordem' => 6],
            ['codigo' => 'ARQ', 'descricao' => 'Arquivado',       'ordem' => 7],
        ];
        foreach ($fases as $f) Fase::firstOrCreate(['codigo' => $f['codigo']], $f);

        // ── Graus de Risco ─────────────────────────────
        $riscos = [
            ['codigo' => 'BAI', 'descricao' => 'Baixo',       'cor_hex' => '#16a34a'],
            ['codigo' => 'MED', 'descricao' => 'Médio',       'cor_hex' => '#d97706'],
            ['codigo' => 'ALT', 'descricao' => 'Alto',        'cor_hex' => '#dc2626'],
            ['codigo' => 'IMP', 'descricao' => 'Improvável',  'cor_hex' => '#64748b'],
        ];
        foreach ($riscos as $r) GrauRisco::firstOrCreate(['codigo' => $r['codigo']], $r);

        // ── Tipos de Ação ──────────────────────────────
        $tiposAcao = [
            ['codigo' => 'TRA', 'descricao' => 'Trabalhista'],
            ['codigo' => 'CIV', 'descricao' => 'Cível'],
            ['codigo' => 'PRE', 'descricao' => 'Previdenciário'],
            ['codigo' => 'TRI', 'descricao' => 'Tributário'],
            ['codigo' => 'FAM', 'descricao' => 'Família'],
            ['codigo' => 'PEN', 'descricao' => 'Penal'],
            ['codigo' => 'ADM', 'descricao' => 'Administrativo'],
            ['codigo' => 'CON', 'descricao' => 'Consumidor'],
        ];
        foreach ($tiposAcao as $t) TipoAcao::firstOrCreate(['codigo' => $t['codigo']], $t);

        // ── Tipos de Processo ──────────────────────────
        $tiposProcesso = [
            ['codigo' => 'ORD', 'descricao' => 'Ordinário'],
            ['codigo' => 'SUM', 'descricao' => 'Sumário'],
            ['codigo' => 'CAU', 'descricao' => 'Cautelar'],
            ['codigo' => 'EXE', 'descricao' => 'Execução'],
            ['codigo' => 'REC', 'descricao' => 'Recurso'],
            ['codigo' => 'MAN', 'descricao' => 'Mandado de Segurança'],
        ];
        foreach ($tiposProcesso as $t) TipoProcesso::firstOrCreate(['codigo' => $t['codigo']], $t);

        // ── Assuntos ───────────────────────────────────
        $assuntos = [
            ['codigo' => 'HEX', 'descricao' => 'Horas Extras'],
            ['codigo' => 'VER', 'descricao' => 'Verbas Rescisórias'],
            ['codigo' => 'DAM', 'descricao' => 'Dano Moral'],
            ['codigo' => 'COB', 'descricao' => 'Cobrança'],
            ['codigo' => 'INA', 'descricao' => 'Indenização por Acidente'],
            ['codigo' => 'DIV', 'descricao' => 'Divórcio'],
            ['codigo' => 'ALI', 'descricao' => 'Alimentos'],
            ['codigo' => 'APO', 'descricao' => 'Aposentadoria'],
            ['codigo' => 'AUX', 'descricao' => 'Auxílio-Doença'],
            ['codigo' => 'EXF', 'descricao' => 'Execução Fiscal'],
        ];
        foreach ($assuntos as $a) Assunto::firstOrCreate(['codigo' => $a['codigo']], $a);

        // ── Repartições ────────────────────────────────
        $reparticoes = [
            ['codigo' => 'VT1', 'descricao' => '1ª Vara do Trabalho'],
            ['codigo' => 'VT2', 'descricao' => '2ª Vara do Trabalho'],
            ['codigo' => 'VT3', 'descricao' => '3ª Vara do Trabalho'],
            ['codigo' => 'VC1', 'descricao' => '1ª Vara Cível'],
            ['codigo' => 'VF1', 'descricao' => 'Vara de Família'],
            ['codigo' => 'JEF', 'descricao' => 'Juizado Especial Federal'],
        ];
        foreach ($reparticoes as $r) Reparticao::firstOrCreate(['codigo' => $r['codigo']], $r);

        // ── Secretarias ────────────────────────────────
        $secretarias = [
            ['codigo' => 'SC1', 'descricao' => 'Secretaria Cível'],
            ['codigo' => 'SC2', 'descricao' => 'Secretaria Trabalhista'],
            ['codigo' => 'SC3', 'descricao' => 'Secretaria Criminal'],
            ['codigo' => 'SC4', 'descricao' => 'Secretaria de Família'],
        ];
        foreach ($secretarias as $s) Secretaria::firstOrCreate(['codigo' => $s['codigo']], $s);

        // ── Índices Monetários ─────────────────────────
        $indices = [
            ['nome' => 'Índice Nacional de Preços ao Consumidor Amplo', 'sigla' => 'IPCA',  'mes_ref' => '2025-01-01', 'percentual' => 0.160000],
            ['nome' => 'Índice Nacional de Preços ao Consumidor Amplo', 'sigla' => 'IPCA',  'mes_ref' => '2025-02-01', 'percentual' => 0.130000],
            ['nome' => 'Índice Geral de Preços - Mercado',              'sigla' => 'IGP-M', 'mes_ref' => '2025-01-01', 'percentual' => 0.220000],
            ['nome' => 'Índice Geral de Preços - Mercado',              'sigla' => 'IGP-M', 'mes_ref' => '2025-02-01', 'percentual' => 0.190000],
            ['nome' => 'Taxa Referencial',                              'sigla' => 'TR',    'mes_ref' => '2025-01-01', 'percentual' => 0.082000],
        ];
        foreach ($indices as $i) IndiceMonetario::firstOrCreate(['sigla' => $i['sigla'], 'mes_ref' => $i['mes_ref']], $i);

        // ── Usuário Admin ──────────────────────────────
        $pessoa = Pessoa::firstOrCreate(
            ['email' => 'admin@sapro.com.br'],
            ['nome' => 'Administrador do Sistema', 'ativo' => true]
        );
        \Illuminate\Support\Facades\DB::table('pessoa_tipos')->insertOrIgnore([
    'pessoa_id' => $pessoa->id,
    'tipo'      => 'Usuário',
]);

        Usuario::firstOrCreate(
            ['login' => 'admin'],
            [
                'pessoa_id' => $pessoa->id,
                'password'  => Hash::make('sapro2025'),
                'perfil'    => 'admin',
                'ativo'     => true,
            ]
        );

        $this->command->info('✅ Dados iniciais inseridos!');
        $this->command->info('🔑 Login: admin | Senha: sapro2025');
    }
}
