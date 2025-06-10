<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Product;
use App\Models\PromotionRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Display a listing of the promotions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $promotions = Promotion::all();

        return view('promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new promotion.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $products = Product::all();
        return view('promotions.create', compact('products'));
    }

    /**
     * Store a newly created promotion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:buy_x_get_y,bulk_discount,combo',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $promotion = Promotion::create([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            if ($request->type === 'buy_x_get_y') {
                $this->createBuyXGetYRule($promotion, $request);
            } elseif ($request->type === 'bulk_discount') {
                $this->createBulkDiscountRule($promotion, $request);
            } elseif ($request->type === 'combo') {
                $this->createComboRule($promotion, $request);
            }

            DB::commit();

            return redirect()->route('promotions.index')
                ->with('success', 'Promotion created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create promotion: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create a buy X get Y free promotion rule.
     *
     * @param  \App\Models\Promotion  $promotion
     * @param  \Illuminate\Http\Request  $request
     * @throws \Exception
     * @return void
     */
    private function createBuyXGetYRule(Promotion $promotion, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'buy_quantity' => 'required|integer|min:1',
            'free_quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Buy X Get Y Free promotion requires: ' . implode(', ', $validator->errors()->all()));
        }

        PromotionRule::create([
            'promotion_id' => $promotion->id,
            'conditions' => [
                'product_id' => $request->product_id,
                'buy_quantity' => $request->buy_quantity,
            ],
            'actions' => [
                'free_quantity' => $request->free_quantity,
            ],
        ]);
    }

    /**
     * Create a bulk discount promotion rule.
     *
     * @param  \App\Models\Promotion  $promotion
     * @param  \Illuminate\Http\Request  $request
     * @throws \Exception
     * @return void
     */
    private function createBulkDiscountRule(Promotion $promotion, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'discounted_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        PromotionRule::create([
            'promotion_id' => $promotion->id,
            'conditions' => [
                'product_id' => $request->product_id,
                'min_quantity' => $request->min_quantity,
            ],
            'actions' => [
                'discounted_price' => $request->discounted_price,
            ],
        ]);
    }

    /**
     * Create a combo promotion rule.
     *
     * @param  \App\Models\Promotion  $promotion
     * @param  \Illuminate\Http\Request  $request
     * @throws \Exception
     * @return void
     */
    private function createComboRule(Promotion $promotion, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array|min:2',
            'product_ids.*' => 'exists:products,id',
            'combo_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        PromotionRule::create([
            'promotion_id' => $promotion->id,
            'conditions' => [
                'product_ids' => $request->product_ids,
            ],
            'actions' => [
                'combo_price' => $request->combo_price,
            ],
        ]);
    }

    /**
     * Display the specified promotion.
     *
     * @param  \App\Models\Promotion  $promotion
     * @return \Illuminate\View\View
     */
    public function show(Promotion $promotion)
    {
        $promotion->load('rules');
        return view('promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified promotion.
     *
     * @param  \App\Models\Promotion  $promotion
     * @return \Illuminate\View\View
     */
    public function edit(Promotion $promotion)
    {
        $products = Product::all();
        $promotion->load('rules');

        return view('promotions.edit', compact('promotion', 'products'));
    }

    /**
     * Update the specified promotion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Promotion  $promotion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $promotion->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? false,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            DB::commit();

            return redirect()->route('promotions.index')
                ->with('success', 'Promotion updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update promotion: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified promotion from storage.
     *
     * @param  \App\Models\Promotion  $promotion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Promotion $promotion)
    {
        try {
            DB::beginTransaction();

            $appliedPromotionsCount = \App\Models\AppliedPromotion::where('promotion_id', $promotion->id)->count();

            if ($appliedPromotionsCount > 0) {
                $promotion->update(['is_active' => false]);

                DB::commit();

                return redirect()->route('promotions.index')
                    ->with('success', 'Promotion has been used in orders and cannot be deleted. It has been marked as inactive instead.');
            }

            $promotion->rules()->delete();

            // Delete the promotion
            $promotion->delete();

            DB::commit();

            return redirect()->route('promotions.index')
                ->with('success', 'Promotion deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete promotion: ' . $e->getMessage());
        }
    }
}
