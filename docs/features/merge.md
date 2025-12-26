# Merge PDFs

The **Merge PDF** feature allows you to combine multiple PDF files into a single file with **fine-grained control over which pages to include or exclude**.

All processing is **local**, ensuring **privacy and security** for sensitive documents like PII, legal, or medical files.

---

## CLI Usage

```bash
php bin/main merge --spec=merge.json
````

* `merge` → command
* `--spec` → path to the JSON spec file

---

## JSON Spec File Format

The JSON file defines:

1. `output.file` → output PDF file path
2. `inputs` → array of input PDFs, each with required `exclude` pages

### Example `merge.json`

```json
{
  "output": {
    "file": "merged.pdf"
  },
  "inputs": [
    {
      "file": "input1.pdf",
      "exclude": [3]
    },
    {
      "file": "input2.pdf",
      "exclude": [1, 2, 4]
    },
    {
      "file": "input3.pdf",
      "include": [1, 2, 3]
    }
  ]
}
```

---

## Spec Semantics

* **`inputs`** are processed in **order**
* **`exclude`**

  * Optional array
  * Pages listed here are removed **after applying `include`**
* **`output.file`** → resulting merged PDF
* **All files remain local**; no data is uploaded or persisted elsewhere