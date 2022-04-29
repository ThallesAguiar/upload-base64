// esta função é para marcar todos os checkbox ao mesmo tempo
// https://www.rafaelwendel.com/2011/11/aprenda-a-marcar-varios-checkbox-de-uma-so-vez/

function verificaStatus(check) {
    if (check.form.checkTodos.checked == 0) {
        check.form.checkTodos.checked = 1;
        desmarcarTodos(check);
    } else {
        check.form.checkTodos.checked = 0;
        marcarTodos(check);
    }
}

function marcarTodos(check) {
    for (i = 0; i < check.form.elements.length; i++)
        if (check.form.elements[i].type == "checkbox")
            check.form.elements[i].checked = 1
}

function desmarcarTodos(check) {
    for (i = 0; i < check.form.elements.length; i++)
        if (check.form.elements[i].type == "checkbox")
            check.form.elements[i].checked = 0
}