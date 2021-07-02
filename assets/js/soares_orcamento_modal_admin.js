const soares_orcamentos = {
    campos:document.querySelector('#campos'),
    addCampo:document.querySelector('button#addCampo'),
    form:document.querySelector('form#soares_orcamento'),
    save:document.querySelector('#submit'),
    addAttr(tag,fields = {}){
        for (const key in fields) {
            tag.setAttribute(key,fields[key]);
        }
    },
    addNewFields(){
        const div = this.cE('div');
        this.addAttr(div,{'class':'campo'});
        const inputTitulo = this.cE('input');
        this.addAttr(inputTitulo,{'placeholder':'Título do campo','type':'text','name':'titulo[]'});
        const remove = this.cE('button');
        const removeText = document.createTextNode('X');
        remove.appendChild(removeText);
        const br = this.cE('br');
        const inputName = this.cE('input');
        this.addAttr(inputName,{'type':'text','placeholder':'Name do Campo','name':'campo[]'});
        const inputObrigatorio = this.cE('input');
        this.addAttr(inputObrigatorio,{'type':'checkbox','name':'obrigatorio[]'});
        const label = this.cE('strong');
        const obrigatorioText = document.createTextNode(' Obrigatório');
        label.appendChild(obrigatorioText);
        div.appendChild(inputTitulo);
        div.appendChild(remove);
        div.appendChild(br);
        div.appendChild(inputName);
        div.appendChild(inputObrigatorio);
        div.appendChild(label);
        this.campos.appendChild(div);
        remove.onclick = (e)=>{
            e.preventDefault();
            e.target.parentNode.remove();
        }
    },
    addClickCampo(){
        this.addCampo.onclick = (e)=>{
            e.preventDefault();
            this.addNewFields();
        };
    },
    cE(tag){
        return document.createElement(tag);
    },
    submitForm(){
        this.form.onsubmit = (r)=>{
            r.preventDefault();

            r.target.querySelectorAll('input,textarea').forEach((v,i)=>{

                console.log(v);

            });

        }
    },
    init(){
        this.addClickCampo();
    }

};
soares_orcamentos.init();