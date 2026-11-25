<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Exibe indicadores simulados e pedidos reais no painel administrativo.
class DashboardController extends Controller
{
    // Carrega os indicadores e aplica o filtro de status aos pedidos.
    public function index(Request $request): View
    {
        $statusInformado = $request->query('status');
        $situacao = is_string($statusInformado) && array_key_exists($statusInformado, config('mock.status', []))
            ? $statusInformado
            : null;

        $pedidosRecentes = Pedido::query()
            ->with(['cliente', 'produtoRelacionado'])
            ->when($situacao, fn ($consulta) => $consulta->where('status', $situacao))
            ->orderByDesc('data_pedido')
            ->orderByDesc('id_pedido')
            ->paginate(8)
            ->withQueryString();

        $painel = config('mock.admin.dashboard');
        $meses = config('mock.admin.months', []);
        $anos = $painel['financial']['years'] ?? [now()->year];
        $anosReceita = $painel['revenue']['years'] ?? [now()->year];
        $mesSelecionado = $this->resolverMes($request->query('mes'));
        $anoSelecionado = $this->resolverAno($request->query('ano'), $anos);

        $painel['financial'] = $this->montarResumoFinanceiro($mesSelecionado, $anoSelecionado, $meses) + [
            'month_options' => $meses,
            'year_options' => $anos,
            'selected_month' => $mesSelecionado,
            'selected_year' => $anoSelecionado,
        ];

        $painel['revenue'] += [
            'year_options' => $anosReceita,
            'selected_year' => $this->resolverAno($request->query('ano_receita'), $anosReceita),
        ];

        return view('admin.dashboard', [
            'painel' => $painel,
            'situacoes' => config('mock.status', []),
            'situacaoAtual' => $situacao,
            'pedidosRecentes' => $pedidosRecentes,
        ]);
    }

    // Usa o mês atual quando o valor recebido não é válido.
    private function resolverMes(mixed $valor): int
    {
        $mes = is_numeric($valor) ? (int) $valor : now()->month;

        return $mes >= 1 && $mes <= 12 ? $mes : now()->month;
    }

    // Aceita somente os anos definidos na configuração.
    private function resolverAno(mixed $valor, array $anos): int
    {
        $ultimoAno = (int) ($anos[array_key_last($anos)] ?? now()->year);
        $ano = is_numeric($valor) ? (int) $valor : (int) now()->year;

        return in_array($ano, array_map('intval', $anos), true) ? $ano : $ultimoAno;
    }

    // Formata os dados financeiros simulados do período escolhido.
    private function montarResumoFinanceiro(int $mes, int $ano, array $meses): array
    {
        $configuracao = config('mock.admin.dashboard.financial', []);
        $dadosMes = $configuracao['months'][$mes] ?? ['revenue' => 0, 'completed' => 0, 'sold' => 0];
        $fatorAno = (float) ($configuracao['year_factors'][$ano] ?? 1);
        $faturamento = (float) $dadosMes['revenue'] * $fatorAno;

        return [
            'period' => ucfirst($meses[$mes] ?? '').' de '.$ano,
            'revenue' => 'R$ '.number_format($faturamento, 2, ',', '.'),
            'completed' => (int) round($dadosMes['completed'] * $fatorAno),
            'sold' => (int) round($dadosMes['sold'] * $fatorAno),
        ];
    }
}
