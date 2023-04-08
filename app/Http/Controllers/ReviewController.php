<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReviewController extends Controller
{
    public function create(Booking $booking)
    {
        $review = Review::where('booking_id', $booking->id)->first();
        return view('reviews.create', compact('booking', 'review'));
    }


    public function store(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $booking   = Booking::findOrFail($bookingId);
        if (!$booking) {
            return redirect()->back()->with('error', __('Booking not found.'));
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if the user is authorized to create a review for this booking
        // if (Auth::user()->cant('createReview', $booking)) {
        //     abort(403);
        // }

        $review = Review::where('booking_id', $booking->id)->first();

        if ($review) {
            return redirect()->back()->with('error', __('You have already reviewed this booking.'));
        }

        // Create the review
// ...


        // Create the review
        $review = new Review([
            'booking_id' => $booking->id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        // Associate the review with the booking and the user
        $review->booking()->associate($booking);
        $review->user()->associate(Auth::user());

        $review->save();

        return redirect()->route('bookings.show', $booking)->with('success', __('Your review has been saved.'));
    }
}