<?php

namespace App\Services\Segment;

use App\Models\Segment;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SegmentService
{
    public function all()
    {
        try {
            $segments = Segment::with(['user']);

            return ['status' => true, 'data' => $segments];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $name = $request->name;
            $status = $request->status;

            $segments = Segment::with(['user']);

            if (isset($name)) {
                $segments->where('name', 'LIKE', "%{$name}%");
            }

            if (isset($status)) {
                $segments->where('status', $status);
            }

            $segments = $segments->paginate($perPage);

            return $segments;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'status' => 'required|in:Active,Inactive',                
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();
            
            $data['user_id'] = Auth::user()->id;

            $segment = Segment::create($data);

            return ['status' => true, 'data' => $segment];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $segment_id)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'status' => 'required|in:Active,Inactive',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors());
            }

            $segment = Segment::find($segment_id);

            if (!$segment) throw new Exception('Segmento não encontrado');

            $segment->update($validator->validated());

            return ['status' => true, 'data' => $segment];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($segment_id)
    {
        try {
            $segment = Segment::find($segment_id);

            if (!$segment) throw new Exception('Segmento não encontrado');

            $segment->delete();

            return ['status' => true];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
