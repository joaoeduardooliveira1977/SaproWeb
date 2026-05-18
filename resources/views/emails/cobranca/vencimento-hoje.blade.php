<x-mail::message>
# Olá, {{ $mensalidade->cliente->nome }}

Sua mensalidade **vence hoje**.

<x-mail::panel>
**{{ $mensalidade->contratoMensal->descricao }}**

Competência: {{ $mensalidade->competenciaFormatada }}
Vencimento: {{ $mensalidade->vencimento->format('d/m/Y') }}
Valor: R$ {{ number_format($mensalidade->valor, 2, ',', '.') }}
Forma de pagamento: {{ strtoupper($mensalidade->contratoMensal->forma_cobranca ?? 'PIX') }}
</x-mail::panel>

Por favor, efetue o pagamento para evitar atraso.

Atenciosamente,
{{ config('mail.from.name') }}
</x-mail::message>
