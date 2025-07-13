
# 📄 Documentação da API – SGHSS

## 🔐 Autenticação

Todas as rotas abaixo, **exceto as de login e registro**, exigem autenticação via token Bearer.

```
Authorization: Bearer {token}
```

---

## 📂 Rotas Públicas

### 1. Registro de Usuário
- **Método**: `POST`
- **Rota**: `/api/register`
- **Descrição**: Registra um novo usuário
- **Body**:
```json
{
  "email": "usuario@exemplo.com",
  "password": "Senha123!"
}
```
- **Resposta**:
```json
{
  "message": "Conta criada com sucesso!"
}
```

### 2. Login de Usuário
- **Método**: `POST`
- **Rota**: `/api/login`
- **Descrição**: Autentica o usuário e retorna um token
- **Body**:
```json
{
  "email": "usuario@exemplo.com",
  "password": "Senha123!"
}
```
- **Resposta**:
```json
{
  "token": "1|abcde...",
  "message": "Logado com sucesso"
}
```

---

## 🔒 Rotas Protegidas

> A partir daqui, todas as requisições requerem token Bearer no header.

### 3. Cadastro de Paciente
- **POST** `/api/patients/register`
```json
{
  "first_name": "Daniel",
  "last_name": "Santos",
  "cpf": "12345678900",
  "phone_number": "(11) 99999-9999",
  "date_birth": "1990-01-01"
}
```

### 4. Cadastro de Enfermeiro
- **POST** `/api/nurses/register`
```json
{
  "user_id": "uuid-do-usuario",
  "first_name": "Tony",
  "last_name": "Chopper",
  "specialtie": 1,
  "cpf": "12345678900",
  "coren": "123456",
  "phone_number": "(11) 98888-8888",
  "date_birth": "1985-06-01"
}
```

### 5. Criar Agenda Semanal
- **POST** `/api/agenda/create`
```json
{
  "days_week": [
    { "day": 1, "start": "08:00", "end": "12:00" },
    { "day": 3, "start": "13:00", "end": "17:00" }
  ]
}
```

### 6. Agendar Consulta
- **POST** `/api/queries/schedule`
```json
{
  "nurses_id": 1,
  "date": "2025-08-01",
  "hour": "08:00",
  "query_type": "Retorno"
}
```

### 7. Reagendar Consulta
- **PUT** `/api/queries/postpone`
```json
{
  "id": 10,
  "date": "2025-08-02",
  "hour": "14:00"
}
```

### 8. Cancelar Consulta
- **DELETE** `/api/queries/cancel`
```json
{
  "id": 10
}
```

### 9. Listar Consultas
- **GET** `/api/queries/list`
**Resposta**:
```json
[
  {
    "id": 1,
    "date": "2025-08-01 08:00:00",
    "query_type": "Rotina",
    "status": "agendada"
  }
]
```

### 10. Ver Agenda do Enfermeiro
- **GET** `/api/agenda/myagenda`

### 11. Atualizar Senha
- **PUT** `/api/user/update/password`
```json
{
  "current_password": "SenhaAntiga123!",
  "password": "NovaSenha123!"
}
```

---

## ✅ Códigos de Resposta

| Código | Significado                    |
|--------|--------------------------------|
| `200`  | OK                             |
| `201`  | Criado com sucesso             |
| `400`  | Requisição inválida            |
| `401`  | Não autorizado (sem token)     |
| `403`  | Acesso negado                  |
| `404`  | Recurso não encontrado         |
| `409`  | Conflito (ex: horário ocupado) |
| `422`  | Erro de validação              |
| `500`  | Erro interno do servidor       |

---

## ✅ Validações Implementadas

- Senhas fortes com regex
- CPF único para pacientes e enfermeiros
- Não agendar consultas em datas passadas
- Impede duplicidade de horário para consultas
- Agendamento só com horário presente na agenda do enfermeiro
- Paciente só pode cancelar ou reagendar suas próprias consultas
