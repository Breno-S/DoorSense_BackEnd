# Referência - DoorSense API

# **LOGIN**

## FAZER LOGIN

## Request `POST` `/login/` `Content-Type: application/json`

### **Parâmetros:**

- **`username`** string `required` - email/username do usuário como consta no banco.
- **`password`** string `required` - senha do usuário como consta no banco.

### **Exemplo:**

```jsx
let headersList = {
 "Content-Type": "application/json"
}

let bodyContent = JSON.stringify({
  "username": "admin",
  "password": "admin"
});

let response = await fetch(baseURL + "login/", { 
  method: "POST",
  body: bodyContent,
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.
- **`token`** string - token de autenticação que será usado em todas as requisições seguintes. Guarde-o no sessionStorage do navegador ou nos cookies.

### **Exemplo:**

```json
{
  "status": "200 OK",
  "message": "Login realizado com sucesso",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJI..."
}
```

## REGISTRAR USUÁRIO (ativado no primeiro acesso)

## Request `PUT` `/login/register-user/` `Authorization: Bearer <JWT>` `Content-Type: application/json`

### **Parâmetros:**

- **`username`** string `required` - email do usuário que será admin do sistema.
- **`password`** string `required` - senha do usuário que será admin do sistema.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI...",
 "Content-Type": "application/json"
}

let bodyContent = JSON.stringify({
  "username": "admin@example.com",
  "password": "s7ven_ate_nin9"
});

let response = await fetch(baseURL + "login/register-user/", { 
  method: "PUT",
  body: bodyContent,
  headers: headersList
});

let data = await response.text();
console.log(data);
```

> ❗ **ATENÇÃO:**
>
> O token que é enviado no cabeçalho `Authorization` deste endpoint deve ser o ticket recebido no endpoint de login ao fazer login com o usuário e senha padrão.

## Response `200` `400` `403` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "Usuário salvo"
}
```

# ALTERAR SENHA

## ESQUECEU A SENHA (receber e-mail de redefinição)

## Request `POST` `/login/forgot-password/`

Por enquanto, este endpoint não recebe parâmetros, assim como não espera um cabeçalho de autorização. Ele simplesmente manda um email de recuperação de senha para o email cadastrado pelo admin do portal. Esta regra de negócio ainda será aprimorada no futuro.

### **Exemplo:**

```jsx
let headersList = {
 "Content-Type": "application/json"
}

let response = await fetch(baseURL + "login/forgot-password/", { 
  method: "POST",
  body: bodyContent,
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "E-mail de recuperação de senha enviado com sucesso"
}
```

## REDEFINIR A SENHA

## Request `PUT` `/login/reset-password/` `Authorization: Bearer <JWT>` `Content-Type: application/json`

### **Parâmetros:**

- **`new-password`** string `required` - nova senha.

### **Exemplo:**

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI...",
 "Content-Type": "application/json"
}

let bodyContent = JSON.stringify({
  "new-password": "KGLW2024"
});

let response = await fetch(baseURL + "login/reset-password/", { 
  method: "PUT",
  body: bodyContent,
  headers: headersList
});

let data = await response.text();
console.log(data);
```

> ❗ **ATENÇÃO:**
>
>  O token que é enviado no cabeçalho `Authorization` deste endpoint deve ser o mesmo que veio no URL do link recebido por e-mail.

## Response `200` `400` `405` `500`

- **`tatus`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "Senha atualizada com sucesso"
}
```

# SALAS

## CRIAR SALA

## Request `POST` `/salas/create/` `Authorization: Bearer <JWT>` `Content-Type: application/json`

### **Parâmetros:**

- **`nome`** string `required` - nome que deseja atribuir a nova sala.
- **`numero`** string `required`  - número que deseja atribuir a nova sala. Para criar uma sala sem número, passe uma string vazia (””) como valor.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI...",
 "Content-Type": "application/json"
}

let bodyContent = JSON.stringify({
  "nome": "Depósito de TI",
  "numero": "" // número é uma string vazia, indicando que a sala não terá número
});

let response = await fetch(baseURL + "salas/create/", { 
  method: "POST",
  body: bodyContent,
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `201` `400` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.
- **`data`** JSON object - objeto contendo os dados da nova sala criada.

### Exemplo:

```json
{
  "status": "201 Created",
  "message": "Sala adicionada com sucesso",
  "data": {
    "id": "8",
    "nome": "Depósito de TI",
    "numero": null,
    "arduino": null,
    "status": null
  }
}
```

## OBTER TODAS AS SALAS

## Request `GET` `/salas/` `Authorization: Bearer <JWT>`

### Parâmetros:

Para obter todas as salas é preciso fazer uma requisição sem query string na URL.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI..."
}

let response = await fetch(baseURL + "salas/", { 
  method: "GET",
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.
- **`data`** JSON object - objeto retornado quando a requisição é bem sucedida.
    - **`total`** int `required` - número de salas cadastradas no banco de dados.
    - **`salas`** array `required` - array contendo as salas e suas informações, separadas como objetos JSON.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "Todas as salas registradas",
  "data": {
    "total": "5",
    "salas": [
      {
        "id": "1",
        "nome": "Laboratório de Informática",
        "numero": "1",
        "arduino": "00 11 22 33 44 55 66 77 88",
        "status": "Ativo"
      },
      {
        "id": "2",
        "nome": "Laboratório de Informática",
        "numero": "2",
        "arduino": "FF EE DD CC BB AA 00 11 22",
        "status": "Ativo"
      },
      {
        "id": "3",
        "nome": "Laboratório de Informática",
        "numero": "3",
        "arduino": null,
        "status": null
      },
      {
        "id": "4",
        "nome": "Laboratório de Informática",
        "numero": "4",
        "arduino": null,
        "status": null
      },
      {
        "id": "5",
        "nome": "Biblioteca",
        "numero": null,
        "arduino": null,
        "status": null
      }
    ]
  }
}
```

## OBTER SALA ESPECÍFICA

## Request `GET` `/salas/` `Authorization: Bearer <JWT>`

### **Parâmetros (query):**

- **`id`** int `required` - ID da sala que se deseja obter os dados.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI..."
}

// A query string na URL define o parâmetro ID
let response = await fetch(baseURL + "salas/?id=" + idSala, { 
  method: "GET",
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `404` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.
- **`data`** JSON object - objeto contendo as informações da sala.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "Sala encontrada",
  "data": {
    "id": 2,
    "nome": "Laboratório de Informática",
    "numero": "2",
    "arduino": "FF EE DD CC BB AA 00 11 22",
    "status": "Ativo"
  }
}
```

## EDITAR SALA

## Request `PUT` `/salas/update/` `Authorization: Bearer <JWT>` `Content-Type: application/json`

### **Parâmetros:**

- **`id`** int `required` - ID da sala que se deseja atualizar.

Além do id, é obrigatório enviar pelo menos um dos seguintes parâmetros:

- **`nome`** string - novo nome que se deseja atribuir a sala.
- **`numero`** string - novo número que se deseja atribuir a sala. Se quiser retirar o número da sala, passe uma string vazia (””) como argumento.
- **`arduino`** string - uniqueID do arduino que se deseja atribuir à sala. Se quiser retirar o arduino da sala, passe uma string vazia (””) como argumento.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI...",
 "Content-Type": "application/json"
}

let bodyContent = JSON.stringify({
  "id": 5,
  "nome": "Library",
  "numero": "420"
});

let response = await fetch(baseURL + "salas/update/", { 
  method: "PUT",
  body: bodyContent,
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `404` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.
- **`data`** JSON object - objeto contendo os novos dados da sala editada.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "Sala atualizada com sucesso",
  "data": {
    "id": "5",
    "nome": "Library",
    "numero": "420",
    "arduino": null,
    "status": null
  }
}
```

## DELETAR SALA

## Request `DELETE` `/salas/delete/` `Authorization: Bearer <JWT>` `Content-Type: application/json`

### **Parâmetros:**

- **`id`** int `required` - ID da sala que deseja deletar.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI...",
 "Content-Type": "application/json"
}

let bodyContent = JSON.stringify({
  "id": 6
});

let response = await fetch(baseURL + "salas/delete/", { 
  method: "DELETE",
  body: bodyContent,
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `404` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "Sala deletada com sucesso"
}
```

# DOORSENSE

## OBTER TODOS OS DOORSENSES

## Request `GET` `/doorsenses/` `Authorization: Bearer <JWT>`

### Parâmetros:

Para obter todos os DoorSenses é preciso fazer uma requisição sem query string na URL.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI..."
}

let response = await fetch(baseURL + "doorsenses/", { 
  method: "GET",
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.
- **`data`** JSON object - objeto retornado quando a requisição é bem sucedida.
    - **`total`** int  `required` - número de DoorSenses cadastradas no banco de dados.
    - **`doorsenses`** array `required` - array contendo os DoorSenses suas informações, separados como objetos JSON.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "Todos os DoorSenses registrados",
  "data": {
    "total": "3",
    "doorsenses": [
      {
        "id": "1",
        "uniqueId": "00 11 22 33 44 55 66 77 88",
        "status": "Ativo",
        "lastUpdate": "2023-08-28 16:49:02"
      },
      {
        "id": "2",
        "uniqueId": "FF EE DD CC BB AA 00 11 22",
        "status": "Ativo",
        "lastUpdate": "2023-08-28 16:49:02"
      },
      {
        "id": "3",
        "uniqueId": "1F 2E 3D 4C 5B 6A 79 88 97",
        "status": "Inativo",
        "lastUpdate": "2023-09-28 14:41:11"
      }
    ]
  }
}
```

## OBTER DOORSENSE ESPECÍFICO

## Request `GET` `/doorsenses/` `Authorization: Bearer <JWT>`

### **Parâmetros (query):**

- **`id`** int `required` - ID (como consta no banco de dados) do DoorSense que deseja obter os dados.

### Exemplo:

```jsx
let headersList = {
 "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJI..."
}

let response = await fetch(baseURL + "doorsenses/?id=" + idDoorsense, { 
  method: "GET",
  headers: headersList
});

let data = await response.text();
console.log(data);
```

## Response `200` `400` `404` `405` `500`

- **`status`** string `required` - status da requisição.
- **`message`** string `required` - mensagem descrevendo o status ou informando o que está sendo retornado.
- **`data`** JSON object - array contendo as informações do DoorSense.

### Exemplo:

```json
{
  "status": "200 OK",
  "message": "DoorSense encontrado",
  "data": {
    "id": 1,
    "uniqueId": "00 11 22 33 44 55 66 77 88",
    "status": "Ativo",
    "lastUpdate": "2023-08-28 16:49:02"
  }
}
```
