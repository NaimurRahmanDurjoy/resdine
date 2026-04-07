import { computed } from 'vue'

/**
 * Composable for generating Laravel resource routes
 * Handles both resource mode (auto-generated) and manual mode
 */
export function useRouteGeneration() {
    const ACTION_SUFFIXES = {
        view: 'index',
        create: 'create',
        edit: 'edit',
        delete: 'destroy', // Laravel convention for delete action
    }

    /**
     * Generate route name for a specific action
     * @param {string} baseRoute - The base route name (e.g., 'users')
     * @param {string} action - The action type (index, create, edit, delete)
     * @returns {string} The generated route name
     */
    const generateRouteForAction = (baseRoute, action) => {
        if (!baseRoute || !action) return ''
        const suffix = ACTION_SUFFIXES[action] || action
        return `${baseRoute}.${suffix}`
    }

    /**
     * Generate routes for multiple actions
     * @param {string} baseRoute - The base route name
     * @param {string[]} actions - Array of action types
     * @returns {Object} Object with action -> route mapping
     */
    const generateRoutesForActions = (baseRoute, actions = []) => {
        if (!baseRoute) return {}

        return actions.reduce((acc, action) => {
            acc[action] = generateRouteForAction(baseRoute, action)
            return acc
        }, {})
    }

    /**
     * Extract base route from a full route name
     * Removes the trailing action segment if it's a known action
     * E.g., 'admin.branches.index' -> 'admin.branches'
     * E.g., 'users' -> 'users' (no change if no action)
     * @param {string} fullRoute - The full route name
     * @returns {string} The base route
     */
    const extractBaseRoute = (fullRoute) => {
        if (!fullRoute) return ''
        
        const segments = fullRoute.split('.')
        if (segments.length < 2) return fullRoute
        
        const lastSegment = segments[segments.length - 1]
        
        // Check if last segment is a known action suffix
        if (['index', 'create', 'edit', 'destroy'].includes(lastSegment)) {
            // Remove the last segment and rejoin
            return segments.slice(0, -1).join('.')
        }
        
        // If last segment is not an action, return as-is
        return fullRoute
    }

    /**
     * Validate if a route name follows Laravel conventions
     * @param {string} route - Route name to validate
     * @returns {boolean}
     */
    const isValidRouteName = (route) => {
        if (!route) return false
        // Route names should be alphanumeric with dots and underscores
        return /^[a-zA-Z][a-zA-Z0-9._-]*$/.test(route)
    }

    /**
     * Get all valid action types
     * @returns {string[]}
     */
    const getValidActions = () => {
        return Object.keys(ACTION_SUFFIXES)
    }

    return {
        ACTION_SUFFIXES,
        generateRouteForAction,
        generateRoutesForActions,
        extractBaseRoute,
        isValidRouteName,
        getValidActions,
    }
}
