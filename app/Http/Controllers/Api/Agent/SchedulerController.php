<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Agent;

use App\Actions\Telemetry\StoreSchedulerExecution;
use App\Actions\Telemetry\StoreSchedulerTask;
use App\Http\Controllers\Controller;
use App\Http\Requests\Telemetry\StoreSchedulerTaskRequest;
use App\Http\Requests\Telemetry\StoreSchedulerExecutionRequest;
use App\Models\AgentToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SchedulerController extends Controller
{
    public function __construct(
        protected StoreSchedulerTask $storeSchedulerTask,
        protected StoreSchedulerExecution $storeSchedulerExecution
    ) {}

    /**
     * Store scheduler task information from the agent.
     *
     * POST /api/agent/scheduler/tasks
     */
    public function storeTask(StoreSchedulerTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $token = $this->getAgentToken($request);

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing agent token.',
            ], 401);
        }

        $project = $token->project;

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
            ], 404);
        }

        try {
            Log::debug('Scheduler task received', [
                'project_id' => $project->id,
                'task_name' => $validated['task_name'] ?? 'unknown',
            ]);

            $task = $this->storeSchedulerTask->execute($project, $validated);

            return response()->json([
                'success' => true,
                'uuid' => $task->uuid,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store scheduler task', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store scheduler task.',
            ], 500);
        }
    }

    /**
     * Store scheduler execution event from the agent.
     *
     * POST /api/agent/scheduler/executions
     */
    public function storeExecution(StoreSchedulerExecutionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $token = $this->getAgentToken($request);

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing agent token.',
            ], 401);
        }

        $project = $token->project;

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
            ], 404);
        }

        try {
            Log::debug('Scheduler execution received', [
                'project_id' => $project->id,
                'execution_uuid' => $validated['execution_uuid'] ?? 'unknown',
                'task_name' => $validated['task_name'] ?? 'unknown',
                'status' => $validated['status'] ?? 'unknown',
            ]);

            $execution = $this->storeSchedulerExecution->execute($project, $validated);

            return response()->json([
                'success' => true,
                'uuid' => $execution->uuid,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store scheduler execution', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store scheduler execution.',
            ], 500);
        }
    }

    /**
     * Get the agent token from the request.
     */
    protected function getAgentToken(Request $request): ?AgentToken
    {
        $tokenValue = $request->input('token')
            ?? $request->header('X-Agent-Token')
            ?? $request->bearerToken();

        if (! $tokenValue) {
            return null;
        }

        $tokenHash = hash('sha256', $tokenValue);

        return AgentToken::where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->first();
    }
}
