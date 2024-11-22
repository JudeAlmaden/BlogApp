<?php

    $row_number = 0;

    if (isset($_SESSION['items']) && !empty($_SESSION['items'])) {
        $row_number = 0; // Ensure row_number is initialized
        $disabled = "";
    
        foreach ($_SESSION['items'] as $item) { // Changed to $_SESSION['items']
            $row_number++;
    
            echo ('<tr class="m-1">
                <th scope="row">' . $row_number . '</th>
                <td>
                    <form method="POST" action="routes.php?operation=updateTask">
                        <input type="text" style="border:none" name="description" ' . $disabled . ' placeholder="' . htmlspecialchars($item['task_info']) . '"> 
                        <input type="submit" name="id" value="' . $item['id'] . '" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
                    </form>
                </td>
                <td>' . htmlspecialchars($item['status']) . '</td>
                <td class="row g-0 m-0 p-0 justify-content-center">
                    <form class="col-5 m-0 p-0" method="POST" action="routes.php?operation=deleteTask"> 
                        <button name="id" value="' . $item['id'] . '" type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-danger col-12">Delete</button>
                    </form>
        
                    <form class="col-5 m-0 p-0" method="POST" action="routes.php?operation=changeTaskStatus"> 
                        <button name="id" value="' . $item['id'] . '" type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-success ms-1 col-12"' . $disabled . '>Finished</button>
                    </form>
                </td>
            </tr>');
        }
    }    
?>