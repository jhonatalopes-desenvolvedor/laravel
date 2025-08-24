import fs from 'fs'
import nodePath from 'path'
import { parse } from '@vue/compiler-sfc'
import { parse as babelParse } from '@babel/parser'
import traverseModule from '@babel/traverse'

const traverse = traverseModule.default

/**
 * Analisa um único arquivo .vue e retorna um objeto com o resultado.
 *
 * @param {string} filePath
 * @returns {{status: string, data?: object, message?: string}}
 */
function analyzeSingleVueFile(filePath) {
    try {
        if (!fs.existsSync(filePath)) {
            return { status: 'error', message: 'File not found.' }
        }

        const content = fs.readFileSync(filePath, 'utf-8')
        const { descriptor } = parse(content)

        const result = {
            type: 'vue_sfc',
            api_style: descriptor.scriptSetup ? 'composition' : 'options',
            prop_count: 0,
            emit_count: 0,
            has_scoped_styles: descriptor.styles.some((s) => s.scoped),
            component_dependencies: [],
            uses_state_management: false,
        }

        const scriptContent = descriptor.script?.content || descriptor.scriptSetup?.content

        if (scriptContent) {
            const ast = babelParse(scriptContent, { sourceType: 'module', plugins: ['typescript'] })

            traverse(ast, {
                CallExpression(astPath) {
                    const calleeName = astPath.node.callee.name
                    if (calleeName === 'defineProps' || calleeName === 'defineEmits') {
                        const arg = astPath.node.arguments[0]
                        let count = 0
                        if (arg) {
                            if (arg.type === 'ArrayExpression') count = arg.elements.length
                            else if (arg.type === 'ObjectExpression') count = arg.properties.length
                        }
                        if (calleeName === 'defineProps') result.prop_count = count
                        else result.emit_count = count
                    }
                },
                ImportDeclaration(astPath) {
                    const source = astPath.node.source.value
                    if (source === 'pinia' || source === 'vuex' || source.includes('store')) {
                        result.uses_state_management = true
                    }
                    if (source.endsWith('.vue')) {
                        const componentName = nodePath.basename(source, '.vue')
                        result.component_dependencies.push(componentName)
                    }
                },
                ExportDefaultDeclaration(astPath) {
                    if (astPath.node.declaration.type === 'ObjectExpression') {
                        astPath.node.declaration.properties.forEach((prop) => {
                            if (prop.key.name === 'props' || prop.key.name === 'emits') {
                                let count = 0
                                if (prop.value.type === 'ArrayExpression') count = prop.value.elements.length
                                else if (prop.value.type === 'ObjectExpression') count = prop.value.properties.length
                                if (prop.key.name === 'props') result.prop_count = count
                                else result.emit_count = count
                            }
                        })
                    }
                },
            })
        }

        result.component_dependencies = [...new Set(result.component_dependencies)]
        return { status: 'success', data: result }
    } catch (e) {
        return { status: 'error', message: `Analysis failed: ${e.message}` }
    }
}

/**
 * Função principal que orquestra a análise em lote.
 */
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
        allResults[filePath] = analyzeSingleVueFile(filePath)
    }

    console.log(JSON.stringify(allResults))
}

main()
