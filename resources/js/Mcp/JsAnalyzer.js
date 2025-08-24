import fs from 'fs'
import { parse as babelParse } from '@babel/parser'
import traverseModule from '@babel/traverse'

const traverse = traverseModule.default

/**
 * @param {string} filePath
 * @returns {{status: string, data?: object, message?: string}}
 */
function analyzeSingleJsFile(filePath) {
    try {
        if (!fs.existsSync(filePath)) {
            return { status: 'error', message: 'File not found.' }
        }

        const content = fs.readFileSync(filePath, 'utf-8')
        const ast = babelParse(content, { sourceType: 'module', plugins: ['typescript'] })

        const result = {
            type: 'javascript_module',
            detected_purpose: 'General Module',
            export_summary: {
                has_default_export: false,
                named_export_count: 0,
            },
            import_summary: {
                imports_vue_api: false,
                imports_state_mgmt: false,
                imports_vue_components: false,
            },
        }

        let foundDefineStore = false
        let foundCreateRouter = false

        traverse(ast, {
            ImportDeclaration(path) {
                const source = path.node.source.value

                if (source.startsWith('vue') || source === 'vue-router') {
                    result.import_summary.imports_vue_api = true
                }

                if (source === 'pinia' || source === 'vuex') {
                    result.import_summary.imports_state_mgmt = true
                }

                if (source.endsWith('.vue')) {
                    result.import_summary.imports_vue_components = true
                }

                path.node.specifiers.forEach((spec) => {
                    if (spec.imported?.name === 'defineStore') foundDefineStore = true
                    if (spec.imported?.name === 'createRouter') foundCreateRouter = true
                })
            },

            ExportDefaultDeclaration() {
                result.export_summary.has_default_export = true
            },

            ExportNamedDeclaration(path) {
                if (path.node.specifiers.length > 0) {
                    result.export_summary.named_export_count += path.node.specifiers.length
                }

                if (path.node.declaration) {
                    result.export_summary.named_export_count++
                }
            },

            CallExpression(path) {
                if (path.node.callee.name === 'defineStore') foundDefineStore = true
                if (path.node.callee.name === 'createRouter') foundCreateRouter = true
            },
        })

        if (foundDefineStore) {
            result.detected_purpose = 'Pinia Store Definition'
        } else if (foundCreateRouter) {
            result.detected_purpose = 'Vue Router Definition'
        } else if (result.export_summary.named_export_count > 0 && !result.export_summary.has_default_export) {
            result.detected_purpose = 'Utility Module'
        } else if (result.export_summary.has_default_export && result.export_summary.named_export_count === 0) {
            result.detected_purpose = 'Configuration/Singleton Module'
        }

        return { status: 'success', data: result }
    } catch (e) {
        return { status: 'error', message: `Analysis failed: ${e.message}` }
    }
}

function main() {
    const jsonPaths = process.argv[2]
    if (!jsonPaths) {
        console.error(JSON.stringify({ error: 'No file paths (JSON array) provided.' }))
        process.exit(1)
    }

    let filePaths
    try {
        filePaths = JSON.parse(jsonPaths)
        if (!Array.isArray(filePaths)) throw new Error('Input is not a valid array of paths.')
    } catch (e) {
        console.error(JSON.stringify({ error: `Invalid JSON input: ${e.message}` }))
        process.exit(1)
    }

    const allResults = {}

    for (const filePath of filePaths) {
        allResults[filePath] = analyzeSingleJsFile(filePath)
    }

    console.log(JSON.stringify(allResults))
}

main()
