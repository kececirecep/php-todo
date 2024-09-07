<?php 
include('db.php');

$edit_task_id = null;
$edit_task_text = '';

// Görev ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task']) && !isset($_POST['edit_task_id'])) {
    $task = $_POST['task'];

    if (!empty($task)) {
        $sql = "INSERT INTO gorevler (text, status) VALUES (:text, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':text', $task);
        $stmt->bindValue(':status', 0);   

        if ($stmt->execute()) { 
            header("Location: ".$_SERVER['PHP_SELF']); 
        } else {
            echo "<script>alert('Görev eklenirken bir hata oluştu.');</script>";
        }
    } else {
        echo "<script>alert('Görev boş olamaz.');</script>";
    }
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_task_id'])) {
    $edit_task_id = $_POST['edit_task_id'];
    $edit_task_text = $_POST['task'];

    if (!empty($edit_task_text)) {
        $sql = "UPDATE gorevler SET text = :text WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':text', $edit_task_text);
        $stmt->bindParam(':id', $edit_task_id);

        if ($stmt->execute()) {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Görev güncellenirken bir hata oluştu.');</script>";
        }
    }
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_task'])) {
    $id = $_POST['delete_task'];

    if (!empty($id)) {
        $sql = "DELETE FROM gorevler WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) { 
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Görev silinirken bir hata oluştu.');</script>";
        }
    }
}
 
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['edit'])) {
    $edit_task_id = $_GET['edit'];
    $sql = "SELECT text FROM gorevler WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $edit_task_id);
    $stmt->execute();
    $edit_task_text = $stmt->fetchColumn();
}

?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Todo Uygulaması</title>

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          container: {
            center: true,
            padding: "2rem",
            screens: {
              sm: "540px",
              md: "668px",
              lg: "824px",
              xl: "9177px",
            },
          },
        },
      },
    };
  </script>
  <link rel="stylesheet" href="style.css" />
</head>

<body class="">
  <div class="container mx-auto">
 
    <div class="shadow p-4 rounded-sm mt-5">
      <div class="flex items-center gap-2 border-b pb-4">
        <span class="bg-[#4FB0FB] text-white p-4 rounded-md flex justify-center w-16 h-16">
          <i class="material-icons flex items-center text-4xl">assignment</i>
        </span>
        <h2 class="text-gray-500"><?php echo $edit_task_id ? 'EDIT ITEM' : 'ADD ITEM'; ?></h2>
      </div>
      <div class="mt-6">
        <form class="w-full flex items-center gap-4" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <input type="text" name="task" placeholder="What do you want to do?" value="<?php echo htmlspecialchars($edit_task_text); ?>"
            class="text-sm p-2 outline-none border-b-2 border-transparent w-full focus:border-b-2 focus:border-[#4FB0FB] placeholder:text-[#4FB0FB]">
          <?php if ($edit_task_id): ?>
            <input type="hidden" name="edit_task_id" value="<?php echo $edit_task_id; ?>">
          <?php endif; ?>
          <button type="submit" class="bg-[#4FB0FB] text-white p-4 rounded-full flex items-center justify-center w-12 h-12">
            <i class="material-icons flex items-center text-3xl"><?php echo $edit_task_id ? 'save' : 'add'; ?></i>
          </button>
        </form>
      </div>
    </div>
 
    <div class="border p-4 rounded-sm mt-5">
      <div class="flex items-center gap-2 border-b pb-4">
        <span class="bg-[#FEB000] text-white p-4 rounded-md flex justify-center w-16 h-16">
          <i class="material-icons flex items-center text-4xl">format_list_bulleted</i>
        </span>
        <h2 class="text-gray-500">TO-DO LIST</h2>
      </div>
 
      <?php
      $sql = "SELECT * FROM gorevler";
      $stmt = $pdo->query($sql);
      $gorevler = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($gorevler as $gorev) {
          echo '<div class="flex justify-between items-center p-4 bg-white border-b border-[#FEB000] mt-4">';
          echo '<span class="text-lg text-gray-700 font-medium w-full">' . htmlspecialchars($gorev['text']) . '</span>';
          echo '<div class="flex gap-2">';
          
          
          echo '<a href="?edit=' . $gorev['id'] . '" class="flex items-center justify-center bg-yellow-400 text-white rounded-full w-10 h-10 hover:bg-yellow-500 transition">';
          echo '<span class="material-icons">mode_edit</span>';
          echo '</a>';
          
          
          echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
          echo '<input type="hidden" name="delete_task" value="' . $gorev['id'] . '">';
          echo '<button type="submit" class="flex items-center justify-center bg-red-500 text-white rounded-full w-10 h-10 hover:bg-red-600 transition">';
          echo '<span class="material-icons">delete</span>';
          echo '</button>';
          echo '</form>';
          
          echo '</div>';
          echo '</div>';
      }
      ?>
    </div>
    
  </div>

</body>

</html>
