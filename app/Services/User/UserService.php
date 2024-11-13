<?php

namespace App\Services\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordRecoveryMail;
use App\Mail\WelcomeMail;
use App\Models\Log;
use App\Models\PasswordRecover;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserService
{
    public function all()
    {
        try {
            $users = User::get();

            return ['status' => true, 'data' => $users];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $name = $request->name;
            $email = $request->email;
            $status = isset($request->status) ? ($request->status == 'Active' ? true : false) : null;

            $users = User::query();

            if(isset($name)){
                $users->where('name', 'LIKE', "%{$name}%");
            }

            if(isset($email)){
                $users->where('email', 'LIKE', "%{$email}%");
            }

            if(isset($status)){
                $users->where('is_active', $status);
            }

            $users = $users->paginate($perPage);

            return $users;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }  

    public function getUser()
    {
        try {
            $user = auth()->user();
    
            if ($user) {
                // Cast para o tipo correto
                $user = $user instanceof \App\Models\User ? $user : \App\Models\User::find($user->id);
    
                return ['status' => true, 'data' => $user];
            }
    
            return ['status' => false, 'error' => 'Usuário não autenticado', 'statusCode' => 401];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'cep' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'number' => 'nullable|string|max:255',
                'neighborhood' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:8',
                'role' => 'nullable|string|in:Seller,Consultant,CommercialManager,TechnicalManager,Admin,Technical,Financial,Copywriter',
            ];

            $password = str_shuffle(Str::upper(Str::random(1)) . rand(0, 9) . Str::random(1, '?!@#$%^&*') . Str::random(5));

            $requestData = $request->all();
            $requestData['password'] = Hash::make($password);
            // Definir valor padrão para is_active aqui, se não fornecido
            $requestData['is_active'] = $request->filled('is_active') ? $request->is_active : true;

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $user = User::create($requestData);

            Log::create([
                "user_id" => Auth::user()->id,
                "action" => "Criou o usuário {$user->name} ({$user->id})",
                "ip" => request()->ip()
            ]);

            Mail::to($user->email)->send(new WelcomeMail($user->name, $user->email, $password));

            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $user_id)
    {
        try {
            // Adicionar condição para ignorar a checagem de unicidade para o ID atual do usuário
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user_id,
                'phone' => 'required|string|max:255',
                'cep' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'number' => 'nullable|string|max:255',                
                'neighborhood' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:8',
                'role' => 'nullable|string|in:Seller,Consultant,CommercialManager,TechnicalManager,Admin,Technical,Financial,Copywriter'];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $userToUpdate = User::find($user_id);

            if (!isset($userToUpdate)) throw new Exception('Usuário não encontrado');

            // Adicionando verificação para is_active apenas se presente na solicitação
            $data = $validator->validated();
            if ($request->has('is_active')) {
                $data['is_active'] = $request->input('is_active');
            }

            $userToUpdate->update($data);

            Log::create([
                "user_id" => Auth::user()->id,
                "action" => "Editou o usuário {$userToUpdate->name} ({$userToUpdate->id})",
                "ip" => request()->ip()
            ]);

            return ['status' => true, 'data' => $userToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function userBlock($user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) throw new Exception('Usuário não encontrado');

            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? "Desbloqueou" : "Bloqueou";
            
            Log::create([
                "user_id" => Auth::user()->id,
                "action" => "$status o usuário {$user->name} ({$user->id})",
                "ip" => request()->ip()
            ]);

            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) throw new Exception('Usuário não encontrado');

            $name = $user->name;
            $id = $user->id;

            if($user->contacts()->count()){
                throw new Exception('Usuário não pode ser deletado pois está vinculado à contatos');
            }

            $user->delete();

            Log::create([
                "user_id" => Auth::user()->id,
                "action" => "Deletou o usuário {$name} ({$id})",
                "ip" => request()->ip()
            ]);

            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function requestRecoverPassword($request)
    {
        try {
            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!isset($user)) throw new Exception('Usuário não encontrado.');

            $code = bin2hex(random_bytes(10));

            $recovery = PasswordRecover::create([
                'code' => $code,
                'user_id' => $user->id,
                'is_active' => true
            ]);

            if (!$recovery) {
                throw new Exception('Erro ao tentar recuperar senha');
            }

            Mail::to($email)->send(new PasswordRecoveryMail($code));

            Log::create([
                    "user_id" => $user->id,
                    "action" => "Solicitou troca de senha",
                    "ip" => request()->ip(),
                ]);

            return ['status' => true, 'data' => $user];

        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function updatePassword($request){
        try{
            $code = $request->code ?? '';
            $code = preg_replace('/[^a-zA-Z0-9]/', '', $request->code);
            $password = $request->password;

            $recovery = PasswordRecover::orderBy('id', 'desc')
                ->where('code', $code)
                ->where('is_active', true)
                ->first();

            if(!isset($recovery)) throw new Exception('Código enviado não é válido.');

            $user = User::find($recovery->user_id);
            $user->password = Hash::make($password);
            $user->save();
            $recovery->is_active = false;
            $recovery->save();

            Log::create([
                "user_id" => $user->id,
                "action" => "Trocou de senha",
                "ip" => request()->ip()
            ]);

            return ['status' => true, 'data' => $user];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
