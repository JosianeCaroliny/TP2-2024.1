<?php
    //TÉCNICA DE PROGRAMAÇÃO II - 3º SEMESTRE 2024
    // Fernanda Vieira de Oliveira
    // Josiane Caroliny da Silva Nascimento
    // Kéllen Jéssica Rocha Silva    
    
    // Início do programa
    
    // Interface 
    interface Observer {
        public function update($message);
    }
    
    // Classes
    class Gestor {
        private static $instance;
        private $clientes = array();
        private $produtos = array();
        private $vendas = array();
    
        private function __construct() {}
        
        //implementação do Singleton
        public static function getInstance() {
            if (self::$instance == null) {  //se a instancia não foi criada cria um novo gestor
                self::$instance = new Gestor();
            }
            return self::$instance;
        }
    
        public function addCliente(Cliente $cliente) {
            $this->clientes[] = $cliente;
        }
    
        public function addProduto(Produto $produto) {
            $this->produtos[] = $produto;
        }
    
        public function addVenda(Venda $venda) {
            $this->vendas[] = $venda;
        }
    
        public function getClientes() {
            return $this->clientes;
        }
    
        public function getProdutos() {
            return $this->produtos;
        }
    
        public function getVendas() {
            return $this->vendas;
        }
    }
    
    class ProductNotifier {
        private $observers = array(); //rmazenar os observadores
                
        //dicionar um observador à lista de observadores
        public function addObserver(Observer $observer) {
            $this->observers[] = $observer;
        }
    
        //notifica todos os observadores registrados quando ocorre uma mudança no estado do produto
        public function notifyObservers($message) {
            foreach ($this->observers as $observer) {
                $observer->update($message);
            }
        }
    }
    
    class Cliente implements Observer {
        protected $nome;
        protected $endereco;
        protected $telefone;
        protected $nascimento;
        protected $status;
        protected $email;
        protected $genero;
        
        private static $contador = 0; //para gerar um novo id a cada novo cliente
        protected $idCliente; //gerado com a propriedade estática acima
        protected $vendas; //array que receberá todas as vendas do cliente
        
        function __construct($nome, $endereco, $telefone, $nascimento, $status, $email, $genero){
            $this->nome = $nome;
            $this->endereco = $endereco;
            $this->telefone = $telefone;
            $this->nascimento = $nascimento;
            $this->status = $status;
            $this->email = $email;
            $this->genero = $genero;
            
            self::$contador++; //contador é incrementado
            $this->idCliente = 'C' . self::$contador; //id recebe o novo valor do contador
            $this->vendas= array(); //inicia como array vazio
        }
        
        public function dadosCliente(){
            echo "Id: " . $this->idCliente . "\n";
            echo "Nome: " . $this->nome . "\n";
            echo "Endereço: " . $this->endereco . "\n";
            echo "Telefone: " . $this->telefone . "\n";
            echo "Data de nascimento: " . $this->nascimento . "\n";
            echo "Status: " . $this->status . "\n";
            echo "Email: " . $this->email . "\n";
            echo "Gênero: " . $this->genero . "\n";
            echo "---\n";
        }
        
        //notificação para novo cliente criado para todos os clientes
        public function update($message) {
        echo "Notificação para {$this->nome}: {$message}\n";
        }
        
        //setters e getters
        public function getIdCliente(){
            return $this->idCliente;
        }
        
        public function getNome() {
            return $this->nome;
        }        
    }
    
    class Venda {
        protected $data;
        protected $valorTot;
        protected $itens; //array para guardar os produtos vendidos [todo]
        protected $cliente; //recebe o cliente cadastrado
        protected $idCliente; //recebe da classe cliente
        protected $idVenda; //recebe o id pela propriedade contador
        private static $contador = 0; //para gerar uma nova id a cada nova venda
                
        function __construct(Cliente $cliente, $idCliente){
            $this->idCliente = $idCliente;
            $this->data = date('Y-m-d H:i:s'); 
            $this->cliente = $cliente; 
            
            self::$contador++; //contador é incrementado
            $this->idVenda = 'PED' . self::$contador; //id recebe o novo valor do contador
            $this->itens = array(); //cria o array para guardar os produtos vendidos
        }
        
        //Cria o produto para venda
        public function addItem(Produto $produto, $quantidade, $desconto){ 
            $item = new Item(); //objeto criado dentro da classe associado a Produto [parte]
            $item->setProduto($produto); //associa o produto no objeto item
            $item->setQuantidade($quantidade);
            $item->setDesconto($desconto);
            
            $item->setPreco($produto->getPreco()); //acessa o preço do produto
            $item->totalItem(); //chama o método para calcular o total do item
            $this->itens[] = $item; //guarda o produto vendido no array
        }
        
        //calcula o total geral da venda [soma todos os itens vendidos]
        public function obterTotal(){
            $total = 0;
            foreach ($this->itens as $item){
                $total += $item->getTotal();
            }
            
            $this->valorTot = $total; //Armazena o total na propriedade $valorTot
            return $total;
        }
       
        //Exibe as vendas
        public function dadosVenda(){
            echo "Id do Pedido: " . $this->idVenda . "\n";
            echo "Data: " . $this->data . "\n";
            
            //exibe os itens da venda
            foreach ($this->itens as $item) {
                echo "Produto: " . $item->getProduto()->getDescricao() . "\n";
                echo "Quantidade: " . $item->getQuantidade() . "\n";
                echo "Desconto: " . $item->getDesconto() . "\n";
                echo "Preço: " . $item->getPreco() . "\n";
                echo "Total do Item: R$" . $item->getTotal() . "\n";
                echo "---\n";
            }
            
            echo "Total do Pedido: " . $this->valorTot . "\n";
            echo "------------------------------------\n";
        }
        
        //contador é decrementado
        public function decrementar(){
            self::$contador--; 
        }
        
        //setters e getters
        public function getCliente() {
            return $this->cliente;
        }
        
        public function getIdVenda(){
            return $this->idVenda;
        }
        
        public function getIdCliente(){
            return $this->idCliente;
        }
    } 
   
    class Item{
        protected $preco;
        protected $quantidade;
        protected $desconto;
        protected $total;
        protected $produto;
        
        //calcula o total do item
        public function totalItem(){
           $this->total = $this->quantidade * $this->preco * (1 - $this->desconto); // 1 - 0.1 (desconto de 10%) = 0.9 * valortotal
        }
        
        //associa o produto ao método addItem
        public function setProduto(Produto $produto){
            $this->produto = $produto;
        }
        
        //setters e getters
        public function setPreco($preco) {
            $this->preco = $preco;
        }
    
        public function setQuantidade($quantidade) {
            $this->quantidade = $quantidade;
        }
    
        public function setDesconto($desconto) {
            $this->desconto = $desconto;
        }
        
        public function getProduto(){
            return $this->produto;
        }
        
        public function getQuantidade(){
            return $this->quantidade;
        }
        
        public function getDesconto(){
            return $this->desconto;
        }
        
        public function getPreco(){
            return $this->preco;
        }
        
        public function getTotal(){
            return $this->total;
        }
    }
    
    class Produto{
        protected $descricao;
        protected $estoque;
        protected $preco;
        protected $medida;
        protected static $contador = 0; // Variável estática para manter o contador de produtos
        protected $idProduto; //gerado com a propriedade estática acima
        
        function __construct ($descricao, $estoque, $preco, $medida){
            $this->descricao = $descricao;
            $this->estoque = $estoque;
            $this->preco = $preco;
            $this->medida = $medida;
            
            self::$contador++; //contador é incrementado
            $this->idProduto = 'P' . self::$contador; //id recebe o novo valor do contador
        }
        
        function dadosProduto(){
            echo "Id: " . $this->idProduto . "\n";
            echo "Descrição: " . $this->descricao . "\n";
            echo "Estoque: " . $this->estoque . "\n";
            echo "Preço: " . $this->preco . "\n";
            echo "Unidade de medida: " . $this->medida . "\n";
            echo "---\n";
        }
        
        //setters e getters
        public function getDescricao(){
            return $this->descricao; 
        }
        
        public function getPreco(){
            return $this->preco;
        }
        
        public function getidProduto(){
            return $this->idProduto;
        }
    } 
   
    $clientesCad = array(); // Declaração da variavel para conseguir validar se o cliente existe
    $produtosCad = array(); // Declaração da variavel para conseguir validar se o produto existe
    $vendasCad = array(); // Declaração da variavel para guardar todas as vendas realizadas
    
    $gestor = Gestor::getInstance();
    $productNotifier = new ProductNotifier();
    
    do{
        // exibe o menu
        echo "------------------------------------\n";
        echo "1- Cadastrar Produto\n";
        echo "2- Listar Produtos\n";
        echo "3- Cadastrar Cliente\n";
        echo "4- Listar Clientes\n";
        echo "5- Cadastrar Venda\n";
        echo "6- Listar Vendas\n";
        echo "7- Imprimir pedido\n";
        echo "0- Sair\n";
        echo "------------------------------------\n";
        
        //recebe a opção do menu
        $menu = fgets(STDIN);
        
        //executa o programa de acordo com o menu
        switch ($menu) {
            case 1:
                
                //solicita os dados ao usuário
                echo "------------------------------------\n";
                $descricao = readline("Descrição do produto: ");
                $estoque = readline("Estoque: ");
                $preco = readline("Preço: ");
                $medida = readline("Unidade de medida [uni]/[pct]: ");
                echo "------------------------------------\n";
                
                //cria um objeto de Produto (novo produto)
                $produto = new Produto($descricao, $estoque, $preco, $medida);
                
                // guarda todos os produtos cadastrados no Gestor
                $gestor->addProduto($produto);
                
                // notifica os clientes sobre o novo produto
                $productNotifier->notifyObservers("Novo produto adicionado: {$descricao}");
               
                break;
            
            case 2: 
                
                //Confere se já existem produtos cadastrados
                if(!empty($gestor->getProdutos())){
                    echo "------------------------------------\n";
                    echo "PRODUTOS CADASTRADOS: \n";
                    
                    // percorre o array de produtos e aciona o método que lista os produtos cadastrado
                    foreach ($gestor->getProdutos() as $itemProduto) {
                    $itemProduto->dadosProduto();
                }
                }else{
                    echo "------------------------------------\n";
                    echo "NENHUM PRODUTO CADASTRADO! \n";
                }
                
                break;
            
            case 3:
                
                //solicita os dados ao usuário
                echo "------------------------------------\n";
                $nome = readline("Nome: ");
                $endereco = readline("Endereço: ");
                $telefone = readline("Telefone: ");
                $nascimento = readline("Data de nascimento: ");
                $status = readline("Status [ativo]/[inativo]: ");
                $email = readline("Email: ");
                $genero = readline("Gênero [f]/[m]: ");
                echo "------------------------------------\n";
                
                //cria um objeto de Cliente (novo cliente)
                $cliente = new Cliente($nome, $endereco, $telefone, $nascimento, $status, $email, $genero);
                
                // guarda todos os clientes cadastrados no Gestor
                $gestor->addCliente($cliente);
                
                // adiciona o cliente como observador de notificações de produtos
                $productNotifier->addObserver($cliente);
                
                break;
            
            case 4:
                
                //Confere se já existem clientes cadastrados
                if(!empty($gestor->getClientes())){
                    echo "------------------------------------\n";
                    echo "CLIENTES CADASTRADOS: \n";
                    
                    // percorre o array de clientes e aciona o método que lista os clientes cadastrados
                    foreach ($gestor->getClientes() as $itemCliente) {
                        $itemCliente->dadosCliente();
                    }
                }else{
                    echo "------------------------------------\n";
                    echo "NENHUM CLIENTE CADASTRADO! \n";
                }
                
                break;
            
            case 5:
                
                // confere se já existem clientes cadastrados
                if (!empty($gestor->getClientes())) {
                    // solicita o ID do cliente
                    echo "------------------------------------\n";
                    $idCliente = readline("Id do cliente: ");
                
                    // percorre o array de clientes em busca do ID
                    foreach ($gestor->getClientes() as $itemCliente) {
                        if ($itemCliente->getIdCliente() == $idCliente) {
                            $cliente = $itemCliente;
                            $clienteEncontrado = true;
                            break;
                        }
                    }
                
                    if (!$clienteEncontrado) {
                        echo "Cliente não cadastrado!\n";
                    } else {
                        // Cria um novo objeto venda
                        $venda = new Venda($cliente, $idCliente);
                
                        // Loop para adicionar itens
                        do {
                            // Variável para validar se o produto existe
                            $produtoEncontrado = false;
                
                            //Solicita o produto a ser adicionado
                            $idProduto = readline("ID do produto: ");
                
                            //percorre o array de produtos cadastrados para localizar o produto solicitado
                            foreach ($gestor->getProdutos() as $produtoDisponivel) {
                
                                if ($produtoDisponivel->getidProduto() == $idProduto) {
                                    //produto foi encontrado
                                    $produtoEncontrado = true;
                
                                    //solicita ao usuário quantidade e desconto
                                    $quantidade = readline("Quantidade: ");
                                    $desconto = readline("Desconto [0.1 = 10%]: ");
                
                                    //chama o método addItem e guarda no array
                                    $venda->addItem($produtoDisponivel, $quantidade, $desconto);
                                };
                            }
                
                            //caso o produto não seja encontrado
                            if ($produtoEncontrado == false) {
                                echo "Produto não cadastrado!\n";
                            }
                
                            echo "------------------------------------\n";
                            echo "1- Adicionar outro item \n";
                            echo "2- Finalizar Pedido \n";
                            echo "0- Cancelar Venda \n";
                            $m = readline();
                
                            if ($m == 2) {
                                //chama o metodo para calcular o total da venda
                                $valorTot = $venda->obterTotal();
                                echo "Total da venda: R$" . $valorTot . "\n";
                
                                //guarda todas as vendas finalizadas/cadastradas
                                $gestor->addVenda($venda);
                            }
                
                            if ($m == 0) {
                                echo "Venda cancelada!\n";
                                $venda->decrementar(); //volta ao valor anterior do idVenda
                                break;
                            }
                        } while ($m != 0 && $m != 2); //loop é interrompido caso 0 ou 2
                    }
                    
                } else {
                    echo "NENHUM CLIENTE CADASTRADO!\n";
                }
                
                break;
                
            case 6:
                
                //Confere se já existem vendas cadastrados
                if(!empty($gestor->getVendas())){
                    echo "------------------------------------\n";
                    echo "\n VENDAS REGISTRADAS:\n";
                    echo "------------------------------------\n";
                    
                    // percorre o array de vendas e aciona o método que lista as vendas cadastradas
                    foreach ($gestor->getVendas() as $itemVenda) {
                        $itemVenda->dadosVenda();
                    }
                }else{
                    echo "------------------------------------\n";
                    echo "NENHUMA VENDA REGISTRADA! \n";
                }
                
                break;
            
            case 7:
                
                //solicita o código do Pedido
                echo "------------------------------------\n";
                $idVenda = readline("Id do Pedido: \n");
                
                //localiza o pedido
                foreach ($gestor->getVendas() as $itemVenda){
                    if ($itemVenda->getIdVenda() === $idVenda){
                        
                        echo "--------IMPRESSÃO DO PEDIDO--------\n";
                        //exibe os dados da venda 
                        $venda->dadosVenda();
                        
                        //localiza o cliente do pedido e exibi os dados
                        $cliente = $venda->getCliente();
                        if ($cliente) {
                            echo "-------- DADOS DO CLIENTE --------\n";
                            $cliente->dadosCliente();
                        } else {
                            echo "Cliente não encontrado para este pedido.\n";
                        }
                                        
                    }
                }
               
                break;
            
            case 0:
                echo "Encerrando o programa...";
                break;
            
            default:
                echo "Entrada inválida!";
                break;
        }
        
    } while ($menu != 0);
?>