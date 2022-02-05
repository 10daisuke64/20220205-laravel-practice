<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Models\Tweet;
use App\Models\User;

use Auth;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 🔽 編集
        $tweets = Tweet::getAllOrderByUpdated_at();
        return view('tweet.index', [
          'tweets' => $tweets
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tweet.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // バリデーション
      $validator = Validator::make($request->all(), [
        'tweet' => 'required | max:191',
        'description' => 'required',
      ]);
      // バリデーション:エラー
      if ($validator->fails()) {
        return redirect()
          ->route('tweet.create')
          ->withInput()
          ->withErrors($validator);
      }

      $data = $request->merge(['user_id' => Auth::user()->id])->all();
       $result = Tweet::create($data);
    
      return redirect()->route('tweet.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $tweet = Tweet::find($id);
      return view('tweet.show', ['tweet' => $tweet]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $tweet = Tweet::find($id);
      return view('tweet.edit', ['tweet' => $tweet]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      //バリデーション
      $validator = Validator::make($request->all(), [
        'tweet' => 'required | max:191',
        'description' => 'required',
      ]);
      //バリデーション:エラー
      if ($validator->fails()) {
        return redirect()
          ->route('tweet.edit', $id)
          ->withInput()
          ->withErrors($validator);
      }
      //データ更新処理
      // updateは更新する情報がなくても更新が走る（updated_atが更新される）
      $result = Tweet::find($id)->update($request->all());
      // fill()save()は更新する情報がない場合は更新が走らない（updated_atが更新されない）
      // $redult = Tweet::find($id)->fill($request->all())->save();
      return redirect()->route('tweet.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $result = Tweet::find($id)->delete();
      //ddd($result);
      return redirect()->route('tweet.index');
    }
    // public function destroy(Tweet $tweet)
    // {
    //   $tweet->delete();
    //   return redirect()->route('tweet.index');
    // }
    
    public function mydata()
    {
      // Userモデルに定義した関数を実行する．
      $tweets = User::find(Auth::user()->id)->mytweets;
      return view('tweet.index', [
        'tweets' => $tweets
      ]);
    }
}
