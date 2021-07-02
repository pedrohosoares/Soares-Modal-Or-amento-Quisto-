<?php

/**
 * Plugin Name:       Soares Orçamento Modal
 * Plugin URI:        mailto:pedrohosoares@gmail.com
 * Description:       Cria botão de orçamentos, armazena os leads e distribui eles para os franqueados.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Pedro Soares
 * Author URI:        https://br.linkedin.com/in/pedro-soares-27657756
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       soares-orcamento-modal
 * Domain Path:       /languages
 */

if(!function_exists('create_table_soares_orcamento_modal')):
    function create_table_soares_orcamento_modal()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "soares_orcamento_modal";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(150) NULL,
        nome varchar(100) NULL,
        cidade varchar(70) NULL,
        estado varchar(70) NULL,
        pais varchar(70) NULL,
        user_id int(10) UNSIGNED NULL, 
        text text NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        aceito INT(1) NULL,
        enviado varchar(20) NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    $wpdb->query($sql);
    }
    
endif;
register_activation_hook(__FILE__, 'create_table_soares_orcamento_modal');


add_action('admin_menu', 'soares_modal_menu'); 
if(!function_exists('soares_modal_menu')):
    function soares_modal_menu(){

        add_menu_page( 'Soares Modal', 'Soares Modal', 'read', 'soares_modal_menu_content', 'soares_modal_menu_content','dashicons-feedback');
        add_menu_page( 'Leads Soares Modal', 'Leads Soares Modal', 'read', 'soares_modal_leads_menu_content', 'soares_modal_leads_menu_content','dashicons-feedback');
        add_menu_page( 'Leads', 'Leads', 'read', 'soares_modal_leads_franqueado_menu_content', 'soares_modal_leads_franqueado_menu_content','dashicons-feedback');
    }

endif;


if(!function_exists('soares_modal_leads_franqueado_menu_content')):

    function soares_modal_leads_franqueado_menu_content(){

        ob_start();
        ?>
        <h1>Leads Capturados</h1><hr />
        
        <table class="wp-list-table widefat fixed striped table-view-list pages">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">Carregando..</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td><button id="prev">Anterior</button></td>
                    <td><button id="next">Próximo</button></td>
                    <td class="pages"></td>
                </tr>
            </tfoot>
        </table>
        <?php
        echo ob_get_clean();

    }

endif;
if(!function_exists('soares_modal_leads_menu_content')):

    function soares_modal_leads_menu_content(){

        ob_start();
        ?>
        <h1>Leads Capturados</h1><hr />
        
        <table class="wp-list-table widefat fixed striped table-view-list pages">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">Carregando..</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td><button id="prev">Anterior</button></td>
                    <td><button id="next">Próximo</button></td>
                    <td class="pages"></td>
                </tr>
            </tfoot>
        </table>
        <?php
        echo ob_get_clean();

    }

endif;

if(!function_exists('soares_modal_menu_content')):

    function soares_modal_menu_content(){

        $data = get_option( 'soares_modal_orcamentos', '' );
        if(!empty($_POST) and isset($_POST)):
            $data = json_encode($_POST);
            update_option( 'soares_modal_orcamentos', $data, false );
        endif;

        ob_start();
        $data = json_decode($data,true);
        ?>
        <h1>Soares Orçamentos</h1><hr />
        <p>
            Os campos email, nome, cidade, estado e pais são recomendados.
        </p>
        <form method="POST" id="soares_orcamento">
            <label>Descrição da Modal</label><br />
            <textarea name="descricao[]" id="" cols="100" rows="5"><?php echo isset($data['descricao'][0])?$data['descricao'][0]:""; ?></textarea>
            <br />
            <label>Adicione os Campos necessários:</label><br />
            <button id="addCampo">+ Campo</button>
            <div id="campos">
                <script>
                    function closeTags(tag){
                        tag.parentNode.remove();
                    }
                </script>
                <?php 
                if(!empty($data)):
                    for ($i=0; $i < count($data['titulo']); $i++): 
                        ?>
                        <div class="campo">
                            <input placeholder="Título do campo" value="<?php echo $data['titulo'][$i]; ?>" type="text" name="titulo[]" />
                            <button onclick="closeTags(this);" style="background:red;color:#FFF;border:none;padding:3px;width:20px;border-radius:3px;cursor:pointer;">X</button>
                            <br />
                            <input type="text" placeholder="Name do Campo"  value="<?php echo $data['campo'][$i]; ?>" name="campo[]" /><input type="checkbox" <?php echo (isset($data['obrigatorio'][$i]) and $data['obrigatorio'][$i] == 'on')?'checked':''; ?> name="obrigatorio[]" id="" /><strong> Obrigatório?</strong>
                        </div>
                        <?php
                    endfor;
                endif;
                ?>
            </div>
            <hr />
            <h3>Regras</h3>
            <hr />
            <p>
            O horário comercial será definido como: <input type="time" name="horario[inicio]" value="<?php echo isset($data['horario']['inicio'])?$data['horario']['inicio']:''; ?>" required placeholder="Horário início" />
             às <input type="time" name="horario[fim]" value="<?php echo isset($data['horario']['fim'])?$data['horario']['fim']:''; ?>" required placeholder="Horário Fim" /> . O prazo para que o franqueado atenda o lead será contado apenas dentro desse horário. 
            </p>
            <br />
            <p>O franqueado terá <input type="number" min="1" value="<?php echo isset($data['tempo_aceitacao'])?$data['tempo_aceitacao']:''; ?>" name="tempo_aceitacao" required placeholder="Apenas números (Horas)" /> horas para aceitar o lead, após esse prazo ele será distribuído novamente.</p>
            <br />
            Se em até <input value="<?php echo isset($data['vida_util'])?$data['vida_util']:''; ?>" type="number" min="1" name="vida_util" required /> horas após o início da contagem da vida útil do Lead ninguém aceitar atende-lo, ele deverá ser direcionado para o email <input type="email" value="<?php echo isset($data['email_quisto'])?$data['email_quisto']:''; ?>" required name="email_quisto" /> da Quisto cuidar do atendimento.
            <br />
            <input id="submit" type="submit" value="<?php echo _e('Salvar','encontreseusite'); ?>">
        </form>
        <?php
        echo ob_get_clean();

    }

endif;

if(!function_exists('soares_orcamento_modal_scripts')):

    function soares_orcamento_modal_scripts($hook){
        
        global $wpdb;
        $idFranquias = "";
        if(is_single() and get_query_var('post_type') == 'soaresshow'):
            $idFranquias = get_the_ID();
        endif;
        $localizacao = $wpdb->get_results("SELECT tt.term_taxonomy_id,t.name,tt.parent FROM {$wpdb->prefix}term_taxonomy tt LEFT JOIN {$wpdb->prefix}terms t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'soares_show_localizacao' ORDER BY tt.parent ASC;");
        $servicos = get_terms(array('taxonomy' => 'soares_show_servicos', 'hide_empty' => false));
        $content = get_option('soares_modal_orcamentos', '' );
        wp_enqueue_script('soares_orcamento_front', plugin_dir_url(__FILE__) . 'assets/js/soares_orcamento_front.js', array(), '1.0.0', true);
        wp_register_script('soares_orcamento_front', plugin_dir_url(__FILE__) . 'assets/js/soares_orcamento_front.js', array(), '1.0.0', true); 
        wp_localize_script('soares_orcamento_front', 'soares_orcamento_front', array('servicos'=>$servicos,'idFranqueado'=>$idFranquias,'localizacoes'=>json_encode($localizacao),'logo'=>show_logo_soares_orcamento(),'content' => $content,'images'=>plugin_dir_url(__FILE__) . 'assets/images/', 'ajaxurl' => admin_url('admin-ajax.php')));
        wp_enqueue_style( 'soares_front', plugin_dir_url( __FILE__ ).'assets/css/public.css',array(), '1.0.0');
    }

endif;
add_action('wp_enqueue_scripts','soares_orcamento_modal_scripts');


if(!function_exists('soares_orcamento_modal_scripts_admin')):

    function soares_orcamento_modal_scripts_admin($hook){

        if('toplevel_page_soares_modal_leads_menu_content' == $hook):
            $action = "soares_orcamentos_ajax_admin";
            $content = array();
            wp_enqueue_script('orcamento_admin_quisto', plugin_dir_url(__FILE__) . 'assets/js/orcamento_admin_quisto.js', array(), '1.0.0', true);
            wp_register_script('orcamento_admin_quisto', plugin_dir_url(__FILE__) . 'assets/js/orcamento_admin_quisto.js', array(), '1.0.0', true); 
            wp_localize_script('orcamento_admin_quisto', 'data', array('action'=>$action,'content' => $content, 'ajaxurl' => admin_url('admin-ajax.php')));
        endif;
        if('toplevel_page_soares_modal_menu_content' == $hook):
            $content = array();
            wp_enqueue_script('soares_orcamento_modal_scripts', plugin_dir_url(__FILE__) . 'assets/js/soares_orcamento_modal_admin.js', array(), '1.0.0', true);
            wp_register_script('soares_orcamento_modal_scripts', plugin_dir_url(__FILE__) . 'assets/js/soares_orcamento_modal_admin.js', array(), '1.0.0', true); 
            wp_localize_script('soares_orcamento_modal_scripts', 'soares_orcamento_modal_scripts_data', array('content' => $content, 'ajaxurl' => admin_url('admin-ajax.php')));
        elseif('toplevel_page_soares_modal_leads_franqueado_menu_content' == $hook):
            $action = "soares_orcamentos_ajax_franqueado";
            $content = array();
            wp_enqueue_script('orcamento_admin_quisto', plugin_dir_url(__FILE__) . 'assets/js/orcamento_franqueado_quisto.js', array(), '1.0.0', true);
            wp_register_script('orcamento_admin_quisto', plugin_dir_url(__FILE__) . 'assets/js/orcamento_franqueado_quisto.js', array(), '1.0.0', true); 
            wp_localize_script('orcamento_admin_quisto', 'data', array('action'=>$action,'content' => $content, 'ajaxurl' => admin_url('admin-ajax.php')));
        endif;
    }

endif;
add_action('admin_enqueue_scripts','soares_orcamento_modal_scripts_admin');


if(!function_exists("soares_orcamentos_ajax_franqueado")):

    function soares_orcamentos_ajax_franqueado(){

        global $wpdb;
        $prefix = $wpdb->prefix;
        $dataLogged = wp_get_current_user();
        $users = ("SELECT pm.post_id FROM {$wpdb->prefix}postmeta pm WHERE pm.meta_key = 'email' AND pm.meta_value = '{$dataLogged->data->user_email}'");
        $users = $wpdb->get_results($users);
        $idUsers = [];
        foreach($users as $i=>$user ):
            $idUsers[] = $user->post_id;
        endforeach;
        $idUsers = implode(',',$idUsers);
        $posts = $wpdb->get_results("SELECT id,email,nome,cidade,estado,enviado,created_at as cadastrado, NULL as ação,aceito FROM {$prefix}soares_orcamento_modal som WHERE som.user_id IN ({$idUsers});");
        echo json_encode($posts);
        exit;

    }

endif;
add_action( 'wp_ajax_soares_orcamentos_ajax_franqueado', 'soares_orcamentos_ajax_franqueado' );


if(!function_exists("soares_modal_leads_confirmacoes_admin_ajax_admin")):

    function soares_modal_leads_confirmacoes_admin_ajax_admin(){

        global $wpdb;
        $id = $_REQUEST['id'];
        if(empty($id)){exit;}
        $date = date('Y-m-d H:i:s');
        $posts = $wpdb->query("UPDATE {$wpdb->prefix}soares_orcamento_modal SET updated_at='{$date}',aceito='1' WHERE id='{$id}'");
        $posts = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}soares_orcamento_modal WHERE id='{$id}'");
        soares_modal_leads_email_aceite_franqueado($posts[0]->email);
        exit;

    }

endif;
add_action( 'wp_ajax_soares_modal_leads_confirmacoes_admin_ajax_admin', 'soares_modal_leads_confirmacoes_admin_ajax_admin' );

if(!function_exists("soares_modal_leads_email_aceite_franqueado")):

    function soares_modal_leads_email_aceite_franqueado($email){

        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $email, "Sua cotação foi visualizada | QUISTO", "Olá! A sua cotação acabou de ser visualizada e esta sendo analisada", $headers );
        
    }

endif;

if(!function_exists("soares_modal_leads_negar_admin_ajax_admin")):

    function soares_modal_leads_negar_admin_ajax_admin(){

        global $wpdb;
        $id = $_REQUEST['id'];
        if(empty($id)){exit;}
        $date = date('Y-m-d H:i:s');
        $posts = $wpdb->query("UPDATE {$wpdb->prefix}soares_orcamento_modal SET updated_at='{$date}',aceito= NULL WHERE id='{$id}'");
        exit;

    }

endif;
add_action( 'wp_ajax_soares_modal_leads_negar_admin_ajax_admin', 'soares_modal_leads_negar_admin_ajax_admin' );

if(!function_exists("ajax_soares_modal_filtro_fields")):

    function ajax_soares_modal_filtro_fields(){

        global $wpdb;
        $data = json_decode(stripslashes($_REQUEST['data']),true);
        $search = [];
        foreach($data as $i=>$v):
            $search[] = $i." LIKE '".$v."%'";
        endforeach;
        $prefix = $wpdb->prefix;
        $search = implode(' AND ',$search);
        $name = $_REQUEST['name'];
        $value = $_REQUEST['value'];
        $dataLogged = wp_get_current_user();
        $users = $wpdb->get_results("SELECT pm.post_id FROM {$wpdb->prefix}postmeta pm WHERE pm.meta_key = 'email' AND pm.meta_value = '{$dataLogged->data->user_email}'");
        $idUsers = [];
        foreach($users as $i=>$user ):
            $idUsers[] = $user->post_id;
        endforeach;
        $idUsers = implode(',',$idUsers);
        $posts = $wpdb->get_results("SELECT id,email,nome,cidade,estado,enviado,text as telefone,text as serviços,created_at as cadastrado, NULL as ação,aceito FROM {$prefix}soares_orcamento_modal WHERE user_id IN ({$idUsers}) AND {$search};");
        echo json_encode($posts);
        exit;

    }

endif;
add_action( 'wp_ajax_ajax_soares_modal_filtro_fields', 'ajax_soares_modal_filtro_fields' );


if(!function_exists("ajax_soares_orcamento_admin_filtro_fields")):

    function ajax_soares_orcamento_admin_filtro_fields(){

        global $wpdb;
        $data = json_decode(stripslashes($_REQUEST['data']),true);
        $search = [];
        foreach($data as $i=>$v):
            $search[] = $i." LIKE '".$v."%'";
        endforeach;
        $prefix = $wpdb->prefix;
        $search = implode(' AND ',$search);
        $name = $_REQUEST['name'];
        $value = $_REQUEST['value'];
        $dataLogged = wp_get_current_user();
        $users = $wpdb->get_results("SELECT pm.post_id FROM {$wpdb->prefix}postmeta pm WHERE pm.meta_key = 'email' AND pm.meta_value = '{$dataLogged->data->user_email}'");
        $idUsers = [];
        foreach($users as $i=>$user ):
            $idUsers[] = $user->post_id;
        endforeach;
        $idUsers = implode(',',$idUsers);
        $posts = $wpdb->get_results("SELECT id,email,nome,cidade,estado,enviado,text as telefone,text as serviços,created_at as cadastrado, NULL as ação FROM {$prefix}soares_orcamento_modal WHERE {$search};");
        echo json_encode($posts);
        exit;

    }

endif;
add_action( 'wp_ajax_ajax_soares_orcamento_admin_filtro_fields', 'ajax_soares_orcamento_admin_filtro_fields' );

if(!function_exists("soares_orcamentos_ajax_admin")):

    function soares_orcamentos_ajax_admin(){

        global $wpdb;
        $prefix = $wpdb->prefix;
        $posts = $wpdb->get_results("SELECT id,email,nome,cidade,estado,enviado,text as telefone,text as serviços,(SELECT u.user_email FROM {$prefix}users u WHERE u.ID = som.user_id) AS proprietario, created_at FROM {$prefix}soares_orcamento_modal som;");
        echo json_encode($posts);
        exit;

    }

endif;
add_action( 'wp_ajax_soares_orcamentos_ajax_admin', 'soares_orcamentos_ajax_admin' );

if (!function_exists('show_logo_soares_orcamento')) :

	function show_logo_soares_orcamento()
	{
		$custom_logo_id = get_theme_mod('custom_logo');
		$logo = wp_get_attachment_image_src($custom_logo_id, 'full');
		if (has_custom_logo()) {
			return '<img style="width:77px;" src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" rel="' . get_bloginfo('name') . '" />';
		}
        return '';
	}

endif;

if (!function_exists('get_data_soares_orcamento')) :

    function get_data_soares_orcamento(){

        $email = isset($_POST['email'])?$_POST['email']:"";
        $nome = isset($_POST['nome'])?$_POST['nome']:"";
        $cidade = isset($_POST['cidade'])?$_POST['cidade']:"";
        $estado = isset($_POST['estado'])?$_POST['estado']:"";
        $pais = isset($_POST['pais'])?$_POST['pais']:"";
        $created_at = date('Y-m-d H:i:s');
        $text = json_encode($_POST);
        $idFranqueado = isset($_POST['idFranqueado'])?$_POST['idFranqueado']:"";
        global $wpdb;
        if(!empty($idFranqueado)):
            $wpdb->query("INSERT INTO {$wpdb->prefix}soares_orcamento_modal (email,nome,cidade,estado,pais,text,created_at,user_id,aceito,enviado) VALUES ('{$email}','{$nome}','{$cidade}','{$estado}','{$pais}','{$text}','{$created_at}','{$idFranqueado}',1,'Não')");
        else:
            $wpdb->query("INSERT INTO {$wpdb->prefix}soares_orcamento_modal (email,nome,cidade,estado,pais,text,created_at,enviado) VALUES ('{$email}','{$nome}','{$cidade}','{$estado}','{$pais}','{$text}','{$created_at}','Não')");
        endif;
        exit;

    }

endif;
add_action( 'wp_ajax_nopriv_get_data_soares_orcamento', 'get_data_soares_orcamento' );
add_action( 'wp_ajax_get_data_soares_orcamento', 'get_data_soares_orcamento' );


if(!function_exists('leads_quisto')):

    function leads_quisto($wpdb,$tempo_aceitacao){

        return $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}soares_orcamento_modal 
            WHERE 
            (aceito IS NULL OR aceito='')
            AND (created_at <= NOW() - INTERVAL {$tempo_aceitacao} MINUTE) 
            ORDER BY id ASC LIMIT 20;"
        );
        
    }

endif;

if(!function_exists('leads_franqueados')):

    function leads_franqueados($wpdb,$tempo_aceitacao,$vida_util){
        
        return $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}soares_orcamento_modal
            WHERE 
            (aceito IS NULL OR aceito='')
            AND 
            (updated_at >= NOW() - INTERVAL {$tempo_aceitacao} MINUTE)
            ORDER BY id ASC LIMIT 20;"
        );
        
    }

endif;

if(!function_exists('recuperar_franqueados')):

    function recuperar_franqueados($wpdb,$route){
        return $wpdb->get_results("SELECT p.ID,p.post_title,pm.meta_value FROM {$wpdb->prefix}posts p 
            LEFT JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.ID
            WHERE
            p.post_type = 'soaresshow'
            AND p.ID > {$route}
            AND meta_key = 'email_soares'
            LIMIT 1;");
            
    }

endif;

if(!function_exists('bl_cron_soares_orcamento')):
    
    function bl_cron_soares_orcamento() {
        if(isset($_GET['cronjob']) and $_GET['cronjob'] == 'orcamento_quisto_913912837__123'):
            
            $dados = get_option( 'soares_modal_orcamentos', '' );
            $dados = json_decode($dados,true);
            $horario_inicio = $dados['horario']['inicio'];
            $horario_fim = $dados['horario']['fim'];
            $tempo_aceitacao = $dados['tempo_aceitacao'] * 60;
            $vida_util = $dados['vida_util'] * 60;
            $email_quisto = $dados['email_quisto'];
            if(date('H') < $horario_inicio and date('H') > $horario_fim):
                exit;
            endif;
            
            global $wpdb;
            $route = get_option( 'soares_orcamento_modal_route',0);
            if(empty($route)):
                $route = 0;
            endif;
            $data = leads_franqueados($wpdb,$tempo_aceitacao,$vida_util);
            if(empty($data)):
                exit;
            endif;

            $countData = count($data);
            try {

                $headers = array('Content-Type: text/html; charset=UTF-8');
                $updated_at = date('Y-m-d H:i:s');
                
                foreach($data as $i=>$lead):
                    $state = $lead->estado;
                    $city = $lead->cidade;
                    /*
                    if(!empty($lead->cidade)):
                        $franquiaPost = $wpdb->get_results(
                            "SELECT * FROM 
                            (SELECT p.ID,t.name,pm.meta_key,pm.meta_value FROM {$wpdb->prefix}posts p 
                            LEFT JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.ID
                            LEFT JOIN {$wpdb->prefix}term_relationships tr ON tr.object_id = p.ID
                            LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                            LEFT JOIN {$wpdb->prefix}terms t ON tt.term_id = t.term_id
                            WHERE
                            p.post_type = 'soaresshow'
                            AND tt.taxonomy = 'soares_show_localizacao'
                            AND t.name = '{$state}'
                            OR t.name = '{$city}'
                            ORDER BY p.ID ASC
                            ) AS ID
                            WHERE 
                            ID > {$route} 
                            AND name = '{$city}'
                            AND meta_key = 'email_soares'
                            LIMIT 1
                            ;"
                        );
                    else:
                        $franquiaPost = $wpdb->get_results(
                            "SELECT * FROM 
                            (SELECT p.ID,t.name,pm.meta_key,pm.meta_value FROM {$wpdb->prefix}posts p 
                            LEFT JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.ID
                            LEFT JOIN {$wpdb->prefix}term_relationships tr ON tr.object_id = p.ID
                            LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                            LEFT JOIN {$wpdb->prefix}terms t ON tt.term_id = t.term_id
                            WHERE
                            p.post_type = 'soaresshow'
                            AND tt.taxonomy = 'soares_show_localizacao'
                            AND t.name = '{$state}'
                            OR t.name = '{$city}'
                            ORDER BY p.ID ASC
                            ) AS ID
                            WHERE 
                            ID > {$route} 
                            AND meta_key = 'email_soares'
                            LIMIT 1
                            ;"
                        );
                    endif;
                    */
                    $text = json_decode($lead->text,true);
                    $email = $lead->email;
                    unset($text['action']);
                    $messagem = "";
                    foreach($text as $nome=>$valor):

                        $messagem .= "<strong>{$nome}</strong>: {$valor}<br />";
                    
                    endforeach;
                    $assunto = "Nova cotação - QUISTO";
                    $franchised = recuperar_franqueados($wpdb,$route);
                    if(empty($franchised)):
                        update_option( 'soares_orcamento_modal_route',0);exit;
                    endif;
                    $ID = $franchised[0]->ID;
                    $emailSend = $franchised[$i]->meta_value;
                    //$emailSend = "pedrohosoares@gmail.com";
                    //wp_mail( $emailSend, $assunto, $messagem, $headers );
                    $sqlUpdated = "UPDATE {$wpdb->prefix}soares_orcamento_modal SET user_id = '{$ID}', updated_at = '{$updated_at}',enviado='E-mail Enviado' WHERE id={$lead->id}";
                    $wpdb->query($sqlUpdated);
                    update_option( 'soares_orcamento_modal_route',$ID);   
                    $route = $ID;

                endforeach;
            } catch (\Throwable $th) {
                //throw $th;
            }
            exit;
        endif;
    }
endif;
add_filter( 'init', 'bl_cron_soares_orcamento' );



if(!function_exists('bl_cron_quisto_soares_orcamento')):
    
    function bl_cron_quisto_soares_orcamento() {
        if(isset($_GET['cronjob']) and $_GET['cronjob'] == 'orcamento_quisto_2_913912837__123'):
            
            $dados = get_option( 'soares_modal_orcamentos', '' );
            $dados = json_decode($dados,true);
            $horario_inicio = $dados['horario']['inicio'];
            $horario_fim = $dados['horario']['fim'];
            $tempo_aceitacao = $dados['tempo_aceitacao'] * 60;
            $vida_util = $dados['vida_util'] * 60;
            $email_quisto = $dados['email_quisto'];
            if(date('H') < $horario_inicio and date('H') > $horario_fim):
                exit;
            endif;
            
            global $wpdb;
            $route = get_option( 'soares_orcamento_modal_route');
            $data = leads_quisto($wpdb,$tempo_aceitacao);
            $ID = "12";
            if(empty($data)):
                exit;
            endif;
            
            $countData = count($data);
            
            try {

                $headers = array('Content-Type: text/html; charset=UTF-8');
                $updated_at = date('Y-m-d H:i:s');
                
                foreach($data as $i=>$lead):
                    $state = $lead->estado;
                    $city = $lead->cidade;
                    $text = json_decode($lead->text,true);
                    $email = $lead->email;
                    unset($text['action']);
                    $messagem = "";
                    foreach($text as $nome=>$valor):

                        $messagem .= "<strong>{$nome}</strong>: {$valor}<br />";
                    
                    endforeach;
                    $assunto = "Nova cotação - QUISTO";
                    $emailSend = $email_quisto;
                    //$emailSend = "pedrohosoares@gmail.com";
                    wp_mail( $emailSend, $assunto, $messagem, $headers );
                    $sqlUpdated = "UPDATE {$wpdb->prefix}soares_orcamento_modal SET aceito=1,user_id = '{$ID}', updated_at = '{$updated_at}',enviado='E-mail Enviado' WHERE id={$lead->id}";
                    $wpdb->query($sqlUpdated);
                    update_option( 'soares_orcamento_modal_route',$ID);   
                    $route = $ID;

                endforeach;
            } catch (\Throwable $th) {
                //throw $th;
            }
            exit;
        endif;
    }
endif;
add_filter( 'init', 'bl_cron_quisto_soares_orcamento' );
