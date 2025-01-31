<?php

use app\Core\Request;
use app\Core\Response;

/**
 * Interface representing the structure of a controller.
 * Defines the methods required for handling standard operations
 * such as creating, retrieving, updating, and deleting resources.
 */
interface ControllerInt
{
    /**
     * Handles the creation process based on the provided request.
     *
     * @param Request $request The request object containing the data for creation.
     * @return Response The response object indicating the result of the creation process.
     */
    public function create(Request $request): Response;

    /**
     * Retrieves the data or resource representation for the index endpoint.
     *
     * @return Response The response object containing the data or resource for the index action.
     */
    public function index(): Response;

    /**
     * Retrieves a resource by its unique identifier from the given request.
     *
     * @param Request $request The request containing the identifier of the resource to retrieve.
     * @return Response The response containing the requested resource or an error if not found.
     */
    public function getById(Request $request): Response;

    /**
     * Updates a resource based on the provided request data.
     *
     * @param Request $request The request containing data to update the resource.
     * @return Response The response indicating the success or failure of the update operation.
     */
    public function update(Request $request): Response;

    /**
     * Deletes a specific resource based on the provided request data.
     *
     * @param Request $request The request object containing the details of the resource to be deleted.
     * @return Response The response object indicating the success or failure of the delete operation.
     */
    public function delete(Request $request): Response;
}