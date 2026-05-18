<x-mail::message>
# Olá, {{ $mensalidade->cliente->nome }}

Você tem uma mensalidade com vencimento em **{{ $diasRestantes }} dia(s)**.

<x-mail::panel>
**{{ $mensalidade->contratoMensal->descricao }}**

Competência: {{ $mensalidade->competenciaFormatada }}
Vencimento: {{ $mensalidade->vencimento->format('d/m/Y') }}
Valor: R$ {{ number_format($mensalidade->valor, 2, ',', '.') }}
Forma de pagamento: {{ strtoupper($mensalidade->contratoMensal->forma_cobranca ?? 'PIX') }}
</x-mail::panel>

Em caso de dúvidas, entre em contato com nosso escritório.

Atenciosamente,
{{ config('mail.from.name') }}
</x-mail::message>
