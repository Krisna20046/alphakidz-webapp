<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

function checkFeedback(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

checkFeedback(route('feedback.index', [], false) === '/profil/feedback', 'Profile feedback URL registered');
checkFeedback(route('feedback.store', [], false) === '/profil/feedback', 'Submission URL registered');
checkFeedback(route('admin-feedback', [], false) === '/admin/feedback', 'Admin inbox URL registered');
checkFeedback(in_array('auth.api', Route::getRoutes()->getByName('feedback.index')->gatherMiddleware()), 'Feedback requires login');

Http::preventStrayRequests();
session(['token' => 'test-token', 'user' => ['id_role' => 3]]);
$controller = new App\Http\Controllers\FeedbackController();
checkFeedback($controller->index()->getData()['adminView'] === false, 'All logged-in roles get submission form without backend queries');
try {
    $controller->adminIndex(Illuminate\Http\Request::create('/admin/feedback'));
    throw new RuntimeException('Non-admin allowed into inbox');
} catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
    checkFeedback($e->getStatusCode() === 403, 'Non-admin inbox blocked');
}
session(['user' => ['id_role' => 1]]);
Http::fake(['*' => Http::response(['success' => true, 'data' => [['id' => 1, 'title' => 'Test']], 'pagination' => ['current_page' => 1]], 200)]);
$view = $controller->adminIndex(Illuminate\Http\Request::create('/admin/feedback'));
checkFeedback(count($view->getData()['allFeedbacks']) === 1, 'Admin inbox reads backend reports');
Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token') && str_ends_with($request->url(), '/feedbacks?page=1'));
echo "PASS: API token stays server-side\n";
