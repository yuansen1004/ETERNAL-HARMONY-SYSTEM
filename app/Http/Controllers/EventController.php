<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function list()
    {
        // Only staff can view event list, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can access event list.');
        }

        $events = Event::all();
        return view('events', compact('events'));
    }

    public function eventsView()
    {
        $events = Event::all();
        return view('eventsView', compact('events'));
    }

    public function create()
    {
        // Only staff can create events, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create events.');
        }

        return view('eventForm');
    }

    public function store(Request $request)
    {
        // Only staff can store events, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create events.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'more_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'sub_description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            $event = new Event();
            $event->name = $request->name;
            $event->description = $request->description;
            $event->sub_description = $request->sub_description;
            $event->start_date = $request->start_date;
            $event->end_date = $request->end_date;

            // Handle main image upload
            if ($request->hasFile('main_image')) {
                $mainImage = $request->file('main_image');
                $mainImageName = time() . '_' . $mainImage->getClientOriginalName();
                $mainImage->move(public_path('images/main'), $mainImageName);
                $event->main_image = 'images/main/' . $mainImageName;
            }

            // Handle additional images upload
            if ($request->hasFile('more_images')) {
                $moreImages = [];
                foreach ($request->file('more_images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images/more'), $imageName);
                    $moreImages[] = 'images/more/' . $imageName;
                }
                $event->more_images = json_encode($moreImages);
            }

            // Set default value for more_images if no images uploaded
            if (!$request->hasFile('more_images')) {
                $event->more_images = json_encode([]);
            }

            $event->save();

            return redirect()->route('events')->with('success', 'Event created successfully!');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Event creation failed: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            
            return back()->withInput()->withErrors(['error' => 'Failed to create event: ' . $e->getMessage()]);
        }
    }

    public function destroy(Event $event)
    {
        // Only staff can delete events, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can delete events.');
        }

        // Delete main image if exists
        if ($event->main_image && file_exists(public_path($event->main_image))) {
            unlink(public_path($event->main_image));
        }

        // Delete additional images if exist
        if ($event->more_images) {
            $moreImages = json_decode($event->more_images, true);
            if (is_array($moreImages)) {
                foreach ($moreImages as $imagePath) {
                    if (file_exists(public_path($imagePath))) {
                        unlink(public_path($imagePath));
                    }
                }
            }
        }

        $eventName = $event->name;
        $event->delete();

        return redirect()->route('events')->with('success', '"' . $eventName . '" deleted successfully.');
    }

    public function view($id)
    {
        $event = Event::findOrFail($id);
        return view('eventsView', compact('event'));
    }

    public function detail($id)
    {
        $event = Event::findOrFail($id);

        $previousEvent = Event::where('id', '<', $event->id)
                                ->orderBy('id', 'desc')
                                ->first();

        $nextEvent = Event::where('id', '>', $event->id)
                          ->orderBy('id', 'asc')
                          ->first();

        return view('eventsDetail', compact('event', 'previousEvent', 'nextEvent'));
    }
    public function edit($id)
    {
        // Only staff can edit events, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can edit events.');
        }

        $event = Event::findOrFail($id);
        return view('editEvent', compact('event'));
    }

    public function update(Request $request, $id)
    {
        // Only staff can update events, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can update events.');
        }

        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'more_images' => 'nullable|array',
                'more_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'required',
                'sub_description' => 'nullable',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $event = Event::findOrFail($id);

            if ($request->hasFile('main_image')) {
                // Delete old main image if exists
                if ($event->main_image && file_exists(public_path($event->main_image))) {
                    unlink(public_path($event->main_image));
                }
                
                $mainImage = $request->file('main_image');
                $mainImageName = time() . '_' . $mainImage->getClientOriginalName();
                $mainImage->move(public_path('images/main'), $mainImageName);
                $event->main_image = 'images/main/' . $mainImageName;
            }

            if ($request->hasFile('more_images')) {
                // Delete old additional images if exist
                if ($event->more_images) {
                    $oldImages = json_decode($event->more_images, true);
                    if (is_array($oldImages)) {
                        foreach ($oldImages as $oldImage) {
                            if (file_exists(public_path($oldImage))) {
                                unlink(public_path($oldImage));
                            }
                        }
                    }
                }
                
                $moreImages = [];
                foreach ($request->file('more_images') as $image) {
                    if ($image->isValid()) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('images/more'), $imageName);
                        $moreImages[] = 'images/more/' . $imageName;
                    }
                }
                $event->more_images = json_encode($moreImages);
            }

            $event->name = $validatedData['name'];
            $event->description = $validatedData['description'];
            $event->sub_description = $validatedData['sub_description'];
            $event->start_date = $validatedData['start_date'];
            $event->end_date = $validatedData['end_date'];

            $event->save();

            return redirect()->route('events')->with('success', '"' . $event->name . '" updated successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update event: ' . $e->getMessage())->withInput();
        }
    }
}