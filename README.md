Vue.js App using Laravel as API (With passport auth).

# Setup transcript (PT)
Se a versão original da database ainda não está criada, deverão seguir o processo "normal":

Substituir a pasta "database" do projeto Laravel pelo conteúdo deste ficheiro (apagar a pasta original).

De seguida executar os seguintes comandos: 

- Para criar a estrutura da base de dados:  
`php artisan migrate`
- Para preencher os dados da base de dados (seed):
`php artisan db:seed`

- Nota: Se ocorrer um erro ao executar o último comando, experimente:
`composer dump-autoload`
`php artisan db:seed`

    Ou em alternativa, no ficheiro "config/app.php" altere o parâmetro timezone para:

`'timezone' => 'Europe/Lisbon'` 

EI@IPL
