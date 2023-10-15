<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagsCollection;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function list()
    {
        return new TagsCollection(Tag::all());
    }
}
