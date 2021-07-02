let table_soares_franqueado_orcamento = {
    formSoares: document.querySelector('form#formSalvarSoares'),
    table: document.querySelector('table'),
    thead: document.querySelector('table thead'),
    tbody: document.querySelector('table tbody'),
    tfoot: document.querySelector('table tfoot'),
    anterior: document.querySelector('table tfoot button#prev'),
    proximo: document.querySelector('table tfoot button#next'),
    paginationNumber: document.querySelector('table tfoot td.pages'),
    limit: 19,
    paginas: 0,
    page: 0,
    min: 0,
    max: 20,
    totalData: 0,
    franqueados: [],
    json: null,
    dataSearchTable:{},
    ajax(url) {
        fetch(url, {
            method: "POST",
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
            },
            body: new URLSearchParams({
                action: data.action,
            })
        })
            .then(response => response.json())
            .then((json) => {
                table_soares_franqueado_orcamento.json = json;
                table_soares_franqueado_orcamento.totalData = json.length;
                table_soares_franqueado_orcamento.rendertable(0, 20);
                table_soares_franqueado_orcamento.clickNext();
                table_soares_franqueado_orcamento.clickPrev();
            });

    },
    limitContentPages(min, max) {
        return table_soares_franqueado_orcamento.json.filter((v, i) => {
            return i >= min && i < max;
        })
    },
    insertColunsTable(data, newTR) {
        let newTD = newTR.insertCell();
        let newText = document.createTextNode(data);
        newTD.appendChild(newText);

    },
    ajaxFilterField(nameField, textFilter) {
        table_soares_franqueado_orcamento.dataSearchTable[nameField] = textFilter;
        console.log(table_soares_franqueado_orcamento.dataSearchTable);
        fetch(data.ajaxurl, {
            method: "POST",
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
            },
            body: new URLSearchParams({
                action: "ajax_soares_modal_filtro_fields",
                data: JSON.stringify(table_soares_franqueado_orcamento.dataSearchTable)
            })
        })
        .then(r => r.json())
        .then((r) => {
                table_soares_franqueado_orcamento.json = r;
                table_soares_franqueado_orcamento.totalData = r.length;
                table_soares_franqueado_orcamento.rendertableBody(0, 20);
                table_soares_franqueado_orcamento.clickNext();
                table_soares_franqueado_orcamento.clickPrev();
        });
    },
    changeFieldSearchTable(input) {
        let timeout = null;
        input.onkeyup = (r) => {
            let textInput = r.target.value;
            let name = r.target.name.split('_')[1];
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                table_soares_franqueado_orcamento.ajaxFilterField(name, textInput);
            }, 1000);
        }
    },
    insertFieldSearchTable(trName, newTR) {
        let newTD = newTR.insertCell();
        let input = document.createElement('input');
        input.style.width = "100%";
        input.name = "_" + trName;
        newTD.appendChild(input);
        this.changeFieldSearchTable(input);

    },
    clickNext() {
        table_soares_franqueado_orcamento.proximo.onclick = (e) => {
            if (table_soares_franqueado_orcamento.page >= (table_soares_franqueado_orcamento.paginas - 1)) {
                return;
            }
            table_soares_franqueado_orcamento.page += 1;
            table_soares_franqueado_orcamento.min += 20;
            table_soares_franqueado_orcamento.max += 20;
            table_soares_franqueado_orcamento.rendertable(table_soares_franqueado_orcamento.min, table_soares_franqueado_orcamento.max);

        };
    },
    clickPrev() {
        table_soares_franqueado_orcamento.anterior.onclick = (e) => {
            if (table_soares_franqueado_orcamento.page < 1) {
                return;
            }
            table_soares_franqueado_orcamento.page -= 1;
            table_soares_franqueado_orcamento.min -= 20;
            table_soares_franqueado_orcamento.max -= 20;
            table_soares_franqueado_orcamento.rendertable(table_soares_franqueado_orcamento.min, table_soares_franqueado_orcamento.max);

        };
    },
    putThead(data) {
        table_soares_franqueado_orcamento.thead.innerHTML = "";
        let newTR = table_soares_franqueado_orcamento.thead.insertRow();
        for (let index = 0; index < data.length; index++) {
            table_soares_franqueado_orcamento.insertFieldSearchTable(data[index], newTR);
        }
        let newTR2 = table_soares_franqueado_orcamento.thead.insertRow();
        for (let index = 0; index < data.length; index++) {
            table_soares_franqueado_orcamento.insertColunsTable(data[index], newTR2);
        }


    },
    cE(tag) {
        return document.createElement(tag);
    },
    rendertable(min, max) {
        this.tbody.innerHTML = "";
        this.paginas = table_soares_franqueado_orcamento.json.length / 20;
        let newJSON = table_soares_franqueado_orcamento.limitContentPages(min, max);
        if (newJSON[0] == undefined) {
            return;
        }
        let keys = Object.keys(newJSON[0]);
        this.putThead(keys);
        newJSON.forEach((dados, indice) => {
            let newTR = table_soares_franqueado_orcamento.tbody.insertRow();
            newTR.style.cursor = "pointer";
            for (let index = 0; index < keys.length; index++) {
                table_soares_franqueado_orcamento.insertColunsTable(dados[keys[index]], newTR);
            }
            let id = newTR.querySelectorAll('td')[0].innerText.trim();
            newTR.onclick = (r) => {
                if (r.target.parentNode == null) {
                    return;
                }
                let countTD = r.target.parentNode.querySelectorAll('td').length;
                let td = r.target.parentNode.querySelectorAll('td')[countTD - 2];
                let aceito = r.target.parentNode.querySelectorAll('td')[countTD-1];
                if(aceito.innerText == "1"){
                    return;
                }
                if (r.target.parentNode.querySelectorAll('td').length == 0) {
                    return;
                }
                let id = r.target.parentNode.querySelectorAll('td')[0].innerText.trim();
                let text = td.innerText.trim();
                let confirmar = table_soares_franqueado_orcamento.cE('button');
                confirmar.innerHTML = "🏆";
                td.innerHTML = "";
                let negar = table_soares_franqueado_orcamento.cE('button');
                negar.innerHTML = "⛔️";
                td.innerHTML = "";
                td.appendChild(confirmar);
                td.appendChild(negar);
                table_soares_franqueado_orcamento.confirmar(confirmar, id);
                table_soares_franqueado_orcamento.negar(negar, id);
            };
        });
        this.putPages();

    },
    rendertableBody(min, max) {
        this.tbody.innerHTML = "";
        this.paginas = table_soares_franqueado_orcamento.json.length / 20;
        let newJSON = table_soares_franqueado_orcamento.limitContentPages(min, max);
        if (newJSON[0] == undefined) {
            return;
        }
        let keys = Object.keys(newJSON[0]);
        newJSON.forEach((dados, indice) => {
            let newTR = table_soares_franqueado_orcamento.tbody.insertRow();
            newTR.style.cursor = "pointer";
            for (let index = 0; index < keys.length; index++) {
                table_soares_franqueado_orcamento.insertColunsTable(dados[keys[index]], newTR);
            }
            //Franqueado
            let id = newTR.querySelectorAll('td')[0].innerText.trim();
            newTR.onclick = (r) => {
                if (r.target.parentNode == null) {
                    return;
                }
                let countTD = r.target.parentNode.querySelectorAll('td').length;
                let td = r.target.parentNode.querySelectorAll('td')[countTD - 2];
                let aceito = r.target.parentNode.querySelectorAll('td')[countTD-1];
                if(aceito.innerText == "1"){
                    return;
                }
                if (r.target.parentNode.querySelectorAll('td').length == 0) {
                    return;
                }
                let id = r.target.parentNode.querySelectorAll('td')[0].innerText.trim();
                let text = td.innerText.trim();
                let confirmar = table_soares_franqueado_orcamento.cE('button');
                confirmar.innerHTML = "🏆";
                td.innerHTML = "";
                let negar = table_soares_franqueado_orcamento.cE('button');
                negar.innerHTML = "⛔️";
                td.innerHTML = "";
                td.appendChild(confirmar);
                td.appendChild(negar);
                table_soares_franqueado_orcamento.confirmar(confirmar, id);
                table_soares_franqueado_orcamento.negar(negar, id);
            };
        });
        this.putPages();

    },
    stopPutKey(input) {
        let count = 1;
        let putContent = 0;
        input.onkeydown = (r) => {
            count = 0;
            putContent = 1;
        }
        if (count && putContent) {

        }
    },
    putFranqueadosInSelect(td) {
        td.innerHTML = '';
        let select = table_soares_franqueado_orcamento.cE('select');
        select.style.width = "100%";
        let option = table_soares_franqueado_orcamento.cE('option');
        option.value = "null";
        option.innerText = "Nenhum";
        select.appendChild(option);
        table_soares_franqueado_orcamento.franqueados.forEach((v, i) => {
            let option = table_soares_franqueado_orcamento.cE('option');
            option.value = v.ID;
            option.innerText = v.display_name;
            select.appendChild(option);
        });
        td.appendChild(select);
        let id = select.parentNode.parentNode.querySelectorAll('td')[0].innerText.trim();
        table_soares_franqueado_orcamento.changeStopWrite(select, id, 'franqueado');
    },
    confirmar(button, id) {
        button.onclick = (r) => {
            r.preventDefault();
            fetch(data.ajaxurl, {
                method: "POST",
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cache-Control': 'no-cache',
                },
                body: new URLSearchParams({
                    action: 'soares_modal_leads_confirmacoes_admin_ajax_admin',
                    id: id
                })
            }).then((r) => {
                button.parentNode.innerHTML = "CONFIRMADO";
            });
        };
    },
    negar(button, id) {
        button.onclick = (r) => {
            r.preventDefault();
            fetch(data.ajaxurl, {
                method: "POST",
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cache-Control': 'no-cache',
                },
                body: new URLSearchParams({
                    action: 'soares_modal_leads_negar_admin_ajax_admin',
                    id: id
                })
            }).then((r) => {
                let tds = button.parentNode.parentNode.querySelectorAll('td');
                tds[6].innerHTML = '';
                button.parentNode.innerHTML = "";

            });
        };
    },
    comentar(button, id) {
        button.onclick = (r) => {
            r.preventDefault();
            const form = table_soares_franqueado_orcamento.cE('form');
            const textarea = table_soares_franqueado_orcamento.cE('textarea');
            textarea.cols = 30;
            textarea.rows = 5;
            let input = table_soares_franqueado_orcamento.cE('input');
            input.type = "submit";
            input.value = "CADASTRAR";
            form.appendChild(textarea);
            form.appendChild(input);
            let tds = button.parentNode.parentNode.querySelectorAll('td');
            tds[7].innerHTML = '';
            tds[7].appendChild(form);

            form.onsubmit = (r) => {
                r.preventDefault();
                fetch(data.ajaxurl, {
                    method: "POST",
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Cache-Control': 'no-cache',
                    },
                    body: new URLSearchParams({
                        action: 'soares_comissoes_comentar_admin_ajax_admin',
                        id: id,
                        message: textarea.value
                    })
                }).then((r) => {
                    tds[7].innerHTML = textarea.value;
                });
            }
        };
    },
    excluir(button, id) {
        button.onclick = (r) => {
            button.parentNode.parentNode.remove();
            fetch(data.ajaxurl, {
                method: "POST",
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cache-Control': 'no-cache',
                },
                body: new URLSearchParams({
                    action: 'soares_comissoes_excluir_admin_ajax_admin',
                    id: id,
                })
            });
        }
    },
    sair(button, id, text) {
        button.onclick = (r) => {
            r.preventDefault();
            button.parentNode.innerHTML = text;
        };
    },
    updateDataWrite(id, text, field) {
        fetch(data.ajaxurl, {
            method: "POST",
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
            },
            body: new URLSearchParams({
                "action": 'soares_comissoes_update_data_field',
                "id": id,
                "field": field,
                "data": text
            })
        });
    },
    waitStopWrite(tag, id, field) {
        let timeout = null;
        tag.onkeyup = (r) => {
            let textInput = r.target.value;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                table_soares_franqueado_orcamento.updateDataWrite(id, textInput, field);
            }, 1000);
        }
    },
    changeStopWrite(tag, id, field) {
        tag.onchange = (r) => {
            let textInput = r.target.value;
            table_soares_franqueado_orcamento.updateDataWrite(id, textInput, field);
        }
    },
    putPages() {
        table_soares_franqueado_orcamento.paginationNumber.innerText = (table_soares_franqueado_orcamento.page + 1) + " de " + parseInt(table_soares_franqueado_orcamento.paginas);
    },
    init(url) {

        this.ajax(url);
    }

}
table_soares_franqueado_orcamento.init(data.ajaxurl);