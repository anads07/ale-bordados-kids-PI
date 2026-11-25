<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Lista pedidos por dia e permite atualizar o status no painel.
class PedidoController extends Controller
{
    // Carrega os pedidos, o calendário e o gráfico do dia selecionado.
    public function index(Request $request): View
    {
        $dataSelecionada = $this->resolverData(
            $request->query('data'),
            $request->query('mes'),
            $request->query('ano'),
        );
        $mesCalendario = $dataSelecionada->copy()->startOfMonth();

        $pedidosDoDia = Pedido::query()
            ->with(['cliente', 'produtoRelacionado'])
            ->whereDate('data_pedido', $dataSelecionada->toDateString())
            ->orderBy('id_pedido')
            ->get();

        $diasComPedidos = Pedido::query()
            ->whereBetween('data_pedido', [
                $mesCalendario->toDateString(),
                $mesCalendario->copy()->endOfMonth()->toDateString(),
            ])
            ->get(['data_pedido'])
            ->map(fn (Pedido $pedido) => $pedido->data_pedido?->toDateString())
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('admin.pedidos.index', [
            'pedidosDoDia' => $pedidosDoDia,
            'dataSelecionada' => $dataSelecionada,
            'mesCalendario' => $mesCalendario,
            'diasComPedidos' => $diasComPedidos,
            'dataAnterior' => $dataSelecionada->copy()->subDay(),
            'dataProxima' => $dataSelecionada->copy()->addDay(),
            'anosCalendario' => config('mock.admin.orders.years', [now()->year]),
            'situacoes' => config('mock.status', []),
            'pedidosMock' => config('mock.admin.orders'),
        ]);
    }

    // Valida e salva o novo status do pedido.
    public function updateStatus(Request $request, Pedido $pedido): RedirectResponse
    {
        $dados = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('mock.status', [])))],
        ]);

        $pedido->update(['status' => $dados['status']]);

        return back()->with('admin_status', 'Status do pedido #'.$pedido->id_pedido.' atualizado.');
    }

    // Usa a data informada ou combina mês e ano; valores inválidos caem em hoje.
    private function resolverData(mixed $dataInformada, mixed $mesInformado = null, mixed $anoInformado = null): Carbon
    {
        if (is_string($dataInformada) && $dataInformada !== '') {
            try {
                return Carbon::createFromFormat('Y-m-d', $dataInformada)->startOfDay();
            } catch (\Throwable) {
                return today();
            }
        }

        if (is_numeric($mesInformado) && is_numeric($anoInformado)) {
            $mes = (int) $mesInformado;
            $ano = (int) $anoInformado;

            if ($mes >= 1 && $mes <= 12 && $ano >= 2000 && $ano <= 2100) {
                return Carbon::create($ano, $mes, 1)->startOfDay();
            }
        }

        return today();
    }
}
