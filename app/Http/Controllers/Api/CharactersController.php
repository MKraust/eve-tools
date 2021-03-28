<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use Illuminate\Http\Request;

class CharactersController extends Controller
{
    public function toggleRole(Request $request) {
        $request->validate([
            'character_id' => 'required|integer',
            'role' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $character = Character::find($request->character_id);
        $field = "is_{$request->role}";
        $character->$field = $request->is_active;
        $character->save();

        return response()->json(['status' => 'success']);
    }
}
