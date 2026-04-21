<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
	public function storeOrUpdate(Request $request)
	{
		$request->validate([
			'vin_id' => 'required|exists:vins,id',
			'usager_id' => 'required|exists:usagers,id',
			'rating' => 'nullable|integer|min:1|max:5',
			'comment' => 'nullable|string'
		]);

		$review = Review::updateOrCreate(
			[
				'usager_id' => $request->usager_id,
				'vin_id' => $request->vin_id
			],
			[
				'rating' => $request->rating,
				'comment' => $request->comment
			]
		);

		return response()->json([
			'message' => 'Review saved.',
			'data' => $review
		]);
	}

	public function show($vin_id, $usager_id)
	{
		$review = Review::where('vin_id', $vin_id)
						->where('usager_id', $usager_id)
						->first();

		return response()->json($review);
	}
}