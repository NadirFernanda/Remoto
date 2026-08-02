{{--
    Este componente já não renderiza nada: 'success'/'status'/'error' e os erros
    de validação ($errors) já são mostrados de forma persistente por cada página
    (ou por layouts.dashboard). Mostrá-los também aqui, como toast temporário que
    desaparece sozinho ao fim de 6s, criava uma mensagem duplicada em todo o
    sistema — uma fixa e outra a desaparecer. Mantido como include vazio para não
    obrigar a mexer em layouts.main.
--}}
